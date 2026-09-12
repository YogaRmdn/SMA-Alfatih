#!/usr/bin/env node
import { spawn, spawnSync } from 'node:child_process';
import net from 'node:net';
import { dirname, join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const isWindows = process.platform === 'win32';
const NODE = process.execPath;
const PHP = 'php';
const NPM = isWindows ? 'npm.cmd' : 'npm';

const SERVER_PORT = 8000;
const VITE_PORT = 5173;

function hexToAnsi(hex) {
    const match = /^#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i.exec(hex);
    if (!match) return '\x1b[0m';
    const [r, g, b] = match.slice(1).map((h) => parseInt(h, 16));
    return `\x1b[38;2;${r};${g};${b}m`;
}

function hasPhpExtension(ext) {
    try {
        const result = spawnSync(PHP, ['-m'], { encoding: 'utf8' });
        return result.status === 0 && result.stdout.toLowerCase().includes(ext.toLowerCase());
    } catch {
        return false;
    }
}

function isPortFree(port) {
    return new Promise((resolvePort) => {
        const server = net.createServer();
        server.once('error', () => resolvePort(false));
        server.once('listening', () => server.close(() => resolvePort(true)));
        server.listen(port, '127.0.0.1');
    });
}

const pailAvailable = hasPhpExtension('pcntl');

const tasks = [
    { name: 'server', color: '#93c5fd', cmd: PHP, args: ['artisan', 'serve'] },
    { name: 'queue', color: '#c4b5fd', cmd: PHP, args: ['artisan', 'queue:listen', '--tries=1', '--timeout=0'] },
    pailAvailable
        ? { name: 'logs', color: '#fb7185', cmd: PHP, args: ['artisan', 'pail', '--timeout=0'] }
        : { name: 'logs', color: '#fb7185', cmd: NODE, args: [join(root, 'scripts', 'tail-log.mjs'), join(root, 'storage', 'logs', 'laravel.log')] },
    isWindows
        ? { name: 'vite', color: '#fdba74', cmd: 'npm', args: ['run', 'dev'], shell: true }
        : { name: 'vite', color: '#fdba74', cmd: 'npm', args: ['run', 'dev'] },
];

const children = [];
let stopping = false;

function stopAll(exitCode) {
    if (stopping) return;
    stopping = true;

    for (const { child } of children) {
        if (child.pid) {
            if (isWindows) {
                spawnSync('taskkill', ['/pid', String(child.pid), '/T', '/F'], { stdio: 'ignore' });
            } else {
                child.kill('SIGTERM');
            }
        }
    }

    process.exit(exitCode);
}

function pipe(child, name, color) {
    const prefix = `${hexToAnsi(color)}[${name}]${hexToAnsi('#64748b')}`;
    const write = (chunk) => {
        const lines = chunk.toString().split('\n');
        for (const line of lines) {
            if (line.length > 0) {
                process.stdout.write(`${prefix} ${line}\x1b[0m\n`);
            }
        }
    };
    child.stdout?.on('data', write);
    child.stderr?.on('data', write);
}

for (const signal of ['SIGINT', 'SIGTERM', 'SIGBREAK']) {
    process.on(signal, () => stopAll(0));
}

const serverPortFree = await isPortFree(SERVER_PORT);
const vitePortFree = await isPortFree(VITE_PORT);

if (!serverPortFree) {
    console.error(`\x1b[91m[dev] Port ${SERVER_PORT} sudah digunakan oleh proses lain.\x1b[0m`);
    console.error(`[dev] Hentikan proses tersebut terlebih dahulu, atau jalankan ulang server lama.\n`);
    process.exit(1);
}

if (!vitePortFree) {
    console.error(`\x1b[91m[dev] Port ${VITE_PORT} sudah digunakan oleh proses lain (mungkin Vite lama).\x1b[0m`);
    console.error(`[dev] Hentikan proses tersebut terlebih dahulu agar Vite dapat berjalan.\n`);
    process.exit(1);
}

console.log(pailAvailable
    ? '[dev] Menjalankan server, queue, pail, dan vite...'
    : '[dev] Pail tidak tersedia di platform ini (membutuhkan ext-pcntl).\n[dev] Sebagai gantinya, log ditampilkan dari storage/logs/laravel.log.\n[dev] Menjalankan server, queue, logs, dan vite...');

for (const task of tasks) {
    let child;
    try {
        child = spawn(task.cmd, task.args, {
            cwd: root,
            shell: task.shell ?? false,
            stdio: ['ignore', 'pipe', 'pipe'],
            windowsHide: true,
        });
    } catch (error) {
        console.error(`\n[dev] Gagal menjalankan [${task.name}]: ${error.message}`);
        stopAll(1);
        continue;
    }

    children.push({ child, ...task });
    pipe(child, task.name, task.color);

    child.on('error', (error) => {
        console.error(`\n[dev] Gagal menjalankan [${task.name}]: ${error.message}`);
        stopAll(1);
    });

    child.on('exit', (code, signal) => {
        if (stopping) return;
        console.error(`\n[dev] Proses [${task.name}] berhenti (code=${code}, signal=${signal}). Menghentikan semua proses...`);
        stopAll(code && code !== 0 ? code : 1);
    });
}
