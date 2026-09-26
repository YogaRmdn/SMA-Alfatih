<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\News;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(SettingsSeeder::class);
    }

    public static function publicPageProvider(): array
    {
        return [
            'beranda' => ['/'],
            'berita' => ['/berita'],
            'unduhan' => ['/unduhan'],
            'tentang-sejarah' => ['/tentang-sejarah'],
            'visi-misi' => ['/visi-misi'],
            'struktur-organisasi' => ['/struktur-organisasi'],
            'ppdb' => ['/ppdb'],
            'ppdb-status' => ['/ppdb/status'],
        ];
    }

    #[DataProvider('publicPageProvider')]
    public function test_public_page_head_is_seo_ready(string $path): void
    {
        $html = $this->get($path)->assertSuccessful()->getContent();

        $this->assertJsonLdValid($html, $path);

        $this->assertMatchesRegularExpression('#<title>[^<]{10,}</title>#', $html, "Title tidak valid di {$path}");
        $this->assertStringContainsString('<meta name="description" content="', $html);
        $this->assertStringContainsString('<link rel="canonical" href="', $html);
        $this->assertStringContainsString('<meta name="robots" content="index, follow', $html);
        $this->assertStringContainsString('<meta name="googlebot" content="index, follow', $html);

        // Sinyal SEO lokal
        $this->assertStringContainsString('<meta name="geo.region" content="ID-RI">', $html);
        $this->assertStringContainsString('<meta name="geo.placename" content="Pekanbaru">', $html);

        // Ikon
        $this->assertStringContainsString('href="http://localhost/favicon.ico"', $html);
        $this->assertStringContainsString('sizes="16x16"', $html);
        $this->assertStringContainsString('sizes="180x180"', $html);
        $this->assertStringContainsString('rel="manifest"', $html);
        $this->assertStringContainsString('name="theme-color"', $html);

        // Open Graph
        $this->assertStringContainsString('property="og:type" content="website"', $html);
        $this->assertStringContainsString('property="og:locale" content="id_ID"', $html);
        $this->assertStringContainsString('property="og:image" content="http://localhost/img/og-default.png"', $html);
    }

    public function test_title_and_description_are_not_double_escaped(): void
    {
        $html = $this->get('/')->assertSuccessful()->getContent();

        $this->assertStringNotContainsString('&amp;amp;', $html, 'Ada meta tag yang ter-escape dua kali');
        $this->assertStringNotContainsString('&amp;#039;', $html, 'Ada meta tag yang ter-escape dua kali');

        preg_match('#<title>(.*?)</title>#s', $html, $title);
        $this->assertSame(
            'SMA IT Tahfizh Al-Fatih Pekanbaru — Sekolah Islam Terpadu &amp; Tahfizh Al-Qur&#039;an',
            $title[1]
        );
    }

    public function test_home_brand_variants_are_in_structured_data(): void
    {
        $html = $this->get('/')->assertSuccessful()->getContent();

        $this->assertStringContainsString('EducationalOrganization', $html);
        $this->assertStringContainsString('WebSite', $html);

        foreach (['SMA Tahfizh Al-Fatih', 'Sekolah Tahfizh Pekanbaru', 'Tahfizh Al-Fatih Pekanbaru'] as $variant) {
            $this->assertStringContainsString($variant, $html, "Varian nama '{$variant}' tidak ada di structured data");
        }
    }

    public function test_news_article_head(): void
    {
        $category = Category::create(['name' => 'Prestasi', 'slug' => 'prestasi']);
        $news = News::create([
            'title' => 'Siswi Raih Juara 1 Lomba Tahfizh & \'Ilmiyyah Pekanbaru',
            'slug' => 'siswi-juara-1-tahfizh-pekanbaru',
            'excerpt' => 'Siswi kami meraih juara 1 lomba tahfizh tingkat kota Pekanbaru.',
            'content' => 'Paragraf pertama. Paragraf kedua. Paragraf ketiga.',
            'category_id' => $category->id,
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);

        $html = $this->get('/berita/'.$news->slug)->assertSuccessful()->getContent();

        $this->assertJsonLdValid($html, '/berita/'.$news->slug);
        $this->assertStringContainsString('property="og:type" content="article"', $html);
        $this->assertStringContainsString('property="article:published_time"', $html);
        $this->assertStringContainsString('property="article:section" content="Prestasi"', $html);
        $this->assertStringContainsString('NewsArticle', $html);
        $this->assertStringContainsString('BreadcrumbList', $html);
        $this->assertStringNotContainsString('&amp;amp;', $html);
    }

    public function test_breadcrumb_schema_present_on_inner_pages(): void
    {
        foreach (['/berita', '/unduhan', '/ppdb', '/tentang-sejarah'] as $path) {
            $this->assertStringContainsString(
                'BreadcrumbList',
                $this->get($path)->assertSuccessful()->getContent(),
                "BreadcrumbList tidak ada di {$path}"
            );
        }
    }

    public function test_robots_meta_respects_section(): void
    {
        Setting::set('ppdb_open', '0');

        $closed = $this->get('/ppdb')->assertSuccessful()->getContent();
        $this->assertStringContainsString('name="robots" content="noindex, follow"', $closed);
    }

    public function test_sitemap_contains_core_pages(): void
    {
        $xml = $this->get('/sitemap.xml')->assertSuccessful()->getContent();

        $this->assertStringContainsString('<urlset', $xml);
        foreach (['/berita', '/unduhan', '/tentang-sejarah', '/visi-misi', '/ppdb'] as $path) {
            $this->assertStringContainsString($path, $xml, "URL {$path} tidak ada di sitemap");
        }

        // XML harus valid
        $prev = libxml_use_internal_errors(true);
        $this->assertNotFalse(simplexml_load_string($xml), 'sitemap.xml tidak valid XML');
        libxml_use_internal_errors($prev);
    }

    public function test_static_seo_assets_exist_and_are_valid(): void
    {
        foreach ([
            'favicon.ico', 'favicon-16x16.png', 'favicon-32x32.png', 'favicon-48x48.png',
            'apple-touch-icon.png', 'android-chrome-192x192.png', 'android-chrome-512x512.png',
            'site.webmanifest', 'img/og-default.png', 'robots.txt',
        ] as $file) {
            $path = public_path($file);
            $this->assertFileExists($path, "Aset {$file} tidak ada");
            $this->assertGreaterThan(0, filesize($path), "Aset {$file} kosong (0 byte)");
        }

        // Header ICO harus signature yang benar (0 byte = ikon globe di Google)
        $ico = file_get_contents(public_path('favicon.ico'));
        $this->assertSame("\x00\x00\x01\x00", substr($ico, 0, 4), 'Header ICO tidak valid');

        $manifest = json_decode(file_get_contents(public_path('site.webmanifest')), true);
        $this->assertIsArray($manifest);
        $this->assertNotEmpty($manifest['icons']);

        [$w, $h] = getimagesize(public_path('img/og-default.png'));
        $this->assertSame(1200, $w);
        $this->assertSame(630, $h);

        foreach (['16x16' => 16, '32x32' => 32, '48x48' => 48] as $suffix => $expected) {
            [$w, $h] = getimagesize(public_path("favicon-{$suffix}.png"));
            $this->assertSame($expected, $w);
            $this->assertSame($expected, $h);
        }

        [$w, $h] = getimagesize(public_path('apple-touch-icon.png'));
        $this->assertSame(180, $w);
        $this->assertSame(180, $h);
    }

    public function test_robots_txt_blocks_sensitive_paths(): void
    {
        $robots = file_get_contents(public_path('robots.txt'));

        $this->assertStringContainsString('Disallow: /admin', $robots);
        $this->assertStringContainsString('Disallow: /ppdb/dokumen/', $robots);
        $this->assertStringContainsString('Sitemap: https://smaittahfizhalfatih.sch.id/sitemap.xml', $robots);
    }

    public function test_admin_pages_are_noindex(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->forceFill([
            'role_id' => Role::where('slug', 'super_admin')->value('id'),
            'email_verified_at' => now(),
        ])->save();

        $html = $this->actingAs($superAdmin)
            ->get('/admin')
            ->assertSuccessful()
            ->getContent();

        $this->assertStringContainsString('name="robots" content="noindex, nofollow"', $html);
        $this->assertStringContainsString('rel="icon" href="http://localhost/favicon.ico"', $html);
    }

    private function assertJsonLdValid(string $html, string $path): void
    {
        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $matches);

        $this->assertNotEmpty($matches[1], "Tidak ada JSON-LD di {$path}");

        foreach ($matches[1] as $index => $json) {
            $decoded = json_decode(trim($json), true);
            $this->assertIsArray(
                $decoded,
                "JSON-LD #{$index} tidak valid di {$path}: ".json_last_error_msg()
            );
            $this->assertArrayHasKey('@type', $decoded);
        }
    }
}
