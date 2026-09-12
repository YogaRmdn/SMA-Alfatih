#!/usr/bin/env node
import { closeSync, existsSync, openSync, readSync, statSync, watch, writeFileSync } from 'node:fs';

const logPath = process.argv[2];

if (!logPath) {
    console.error('usage: node tail-log.mjs <path>');
    process.exit(1);
}

if (!existsSync(logPath)) {
    writeFileSync(logPath, '', 'utf8');
}

let pos = statSync(logPath).size;

function drain() {
    let size;
    try {
        size = statSync(logPath).size;
    } catch {
        return;
    }

    if (size < pos) {
        pos = 0;
    }

    if (size > pos) {
        const fd = openSync(logPath, 'r');
        const buffer = Buffer.alloc(size - pos);
        readSync(fd, buffer, 0, buffer.length, pos);
        closeSync(fd);
        pos = size;

        const text = buffer.toString('utf8');
        if (text) {
            process.stdout.write(text);
        }
    }
}

drain();
watch(logPath, (event) => {
    if (event === 'change' || event === 'rename') {
        drain();
    }
});

for (const signal of ['SIGINT', 'SIGTERM', 'SIGBREAK']) {
    process.on(signal, () => process.exit(0));
}
