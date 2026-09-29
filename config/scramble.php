<?php

use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;

return [
    /*
     * Which routes to document. String or array form; use Scramble::routes() for custom selection.
     *
     * 'api_path' => [
     *     'include' => 'api',
     *     'exclude' => ['api/internal'],
     * ],
     *
     * Without *, patterns match path segments (api matches api and api/users, not apiary).
     * With *, Str::is is used (e.g. api/v*).
     *
     * One static include → default server is /{include} and paths are stripped (/users).
     * Multiple includes or wildcards → server defaults to / and paths stay full (/api/users).
     * Override with `servers`, or use Scramble::registerApi() for separate bases.
     */
    'api_path' => 'api',

    /*
     * Your API domain. By default, app domain is used. This is also a part of the default API routes
     * matcher, so when implementing your own, make sure you use this config if needed.
     */
    'api_domain' => null,

    /*
     * The path where your OpenAPI specification will be exported.
     */
    'export_path' => 'api.json',

    /*
     * Cache configuration for the generated OpenAPI document.
     *
     * Use `scramble:cache` to warm the cache and `scramble:clear` to invalidate it.
     */
    'cache' => [
        'key' => 'scramble.openapi',
        'store' => 'file',
    ],

    'info' => [
        /*
         * API version.
         */
        'version' => env('API_VERSION', '0.0.1'),

        /*
         * Description rendered on the home page of the API documentation (`/docs/api`).
         */
        'description' => <<<'MARKDOWN'
        # KILAT API

    KILAT API menyediakan akses terstruktur terhadap data berita dan ringkasan informasi yang dikumpulkan dari berbagai Organisasi Perangkat Daerah (OPD) di Provinsi Kepulauan Bangka Belitung.

    ## Autentikasi

    Setiap request ke API membutuhkan **API Token**.

    API Token dapat diperoleh melalui halaman **API Client** pada panel administrator KILAT setelah sistem atau aplikasi yang akan menggunakan API didaftarkan.

    Token dikirim melalui HTTP Header menggunakan format:

    ```text
    Authorization: Bearer {TOKEN}

    Contoh:

    Authorization: Bearer eyJ...

    Jangan membagikan API Token kepada pihak yang tidak berwenang.

    Base URL

    Untuk lingkungan development:

    http://127.0.0.1:8000/api/v1

    Pada lingkungan production, gunakan domain resmi KILAT.

    Endpoint Berita

    Endpoint berita digunakan untuk mengambil berita yang telah memiliki ringkasan dari sistem KILAT.

    1. Mengambil Semua Berita
    GET /api/v1/berita

    Contoh:

    GET /api/v1/berita

    Endpoint akan mengembalikan berita terbaru terlebih dahulu.

    2. Filter Berdasarkan OPD

    Gunakan parameter opd_id untuk mengambil berita dari OPD tertentu.

    GET /api/v1/berita?opd_id={opd_id}

    Contoh:

    GET /api/v1/berita?opd_id=19
    3. Filter Berdasarkan Kategori

    Gunakan parameter kategori_id untuk mengambil berita berdasarkan kategori tertentu.

    GET /api/v1/berita?kategori_id={kategori_id}

    Contoh:

    GET /api/v1/berita?kategori_id=4
    4. Pencarian Berita

    Gunakan parameter search untuk mencari berita berdasarkan judul atau ringkasan.

    GET /api/v1/berita?search={kata_kunci}

    Contoh:

    GET /api/v1/berita?search=ekspor
    5. Kombinasi OPD + Kategori

    Parameter opd_id dan kategori_id dapat digunakan secara bersamaan.

    GET /api/v1/berita?opd_id={opd_id}&kategori_id={kategori_id}

    Contoh:

    GET /api/v1/berita?opd_id=19&kategori_id=4
    6. Kombinasi OPD + Pencarian

    Parameter opd_id dan search dapat digunakan secara bersamaan.

    GET /api/v1/berita?opd_id={opd_id}&search={kata_kunci}

    Contoh:

    GET /api/v1/berita?opd_id=19&search=ekspor
    7. Kombinasi Kategori + Pencarian

    Parameter kategori_id dan search dapat digunakan secara bersamaan.

    GET /api/v1/berita?kategori_id={kategori_id}&search={kata_kunci}

    Contoh:

    GET /api/v1/berita?kategori_id=4&search=ekspor
    8. Kombinasi Tiga Filter

    Ketiga parameter dapat digunakan secara bersamaan untuk pencarian yang lebih spesifik.

    GET /api/v1/berita?opd_id={opd_id}&kategori_id={kategori_id}&search={kata_kunci}

    Contoh:

    GET /api/v1/berita?opd_id=19&kategori_id=4&search=ekspor

    Request tersebut akan mengambil berita yang memenuhi seluruh kondisi berikut:

    berasal dari OPD dengan ID 19;
    memiliki kategori dengan ID 4;
    judul atau ringkasannya mengandung kata ekspor.
    Pagination

    Jumlah data dalam satu halaman dapat diatur menggunakan parameter per_page.

    Nilai yang diperbolehkan adalah 1 sampai 100.

    Contoh:

    GET /api/v1/berita?per_page=20

    Parameter tersebut juga dapat dikombinasikan dengan filter lainnya.

    Contoh:

    GET /api/v1/berita?opd_id=19&kategori_id=4&search=ekspor&per_page=20
    Format Response

    API menggunakan format JSON.

    Response berhasil memiliki struktur umum:

    {
        "success": true,
        "message": "Data berita berhasil diambil.",
        "data": [],
        "meta": {
            "current_page": 1,
            "per_page": 10,
            "total": 0,
            "last_page": 1
        }
    }
    Endpoint Lain

    Selain endpoint berita, KILAT API juga menyediakan:

    GET /api/v1/berita/{id}

    Untuk mengambil detail satu berita.

    GET /api/v1/ringkasan

    Untuk mengambil ringkasan periodik KILAT.

    Dokumentasi detail masing-masing endpoint tersedia pada bagian endpoint API di bawah halaman dokumentasi.
    MARKDOWN,
    ],
    

    'ui' => [
        'title' => null,
    ],

    /*
     * Load Scramble's development tools on documentation pages. An explicit
     * SCRAMBLE_DEV_TOOLS value takes precedence over APP_DEBUG.
     */
    'dev_tools' => [
        'enabled' => env('SCRAMBLE_DEV_TOOLS', env('APP_DEBUG', false)),
    ],

    'renderer' => 'elements',

    'renderers' => [
        /*
         * Stoplight Elements config options: https://docs.stoplight.io/docs/elements/b074dc47b2826-elements-configuration-options
         */
        'elements' => [
            'view' => 'scramble::docs',
            'theme' => 'light',
            'hideTryIt' => false,
            'hideSchemas' => false,
            'logo' => '',
            'tryItCredentialsPolicy' => 'include',
            'layout' => 'responsive',
            'router' => 'hash',
        ],
        /*
         * Scalar API reference config options: https://scalar.com/products/api-references/configuration
         */
        'scalar' => [
            'view' => 'scramble::scalar',
            'cdn' => 'https://cdn.jsdelivr.net/npm/@scalar/api-reference',
            'theme' => 'laravel',
            'proxyUrl' => 'https://proxy.scalar.com',
            'darkMode' => false,
            'showDeveloperTools' => 'never',
            'agent' => ['disabled' => true],
            'credentials' => 'include',
        ],
    ],

    /*
     * The list of servers of the API. By default, when `null`, server URL will be created from
     * `scramble.api_path` and `scramble.api_domain` config variables. When providing an array, you
     * will need to specify the local server URL manually (if needed).
     *
     * Example of non-default config (final URLs are generated using Laravel `url` helper):
     *
     * ```php
     * 'servers' => [
     *     'Live' => 'api',
     *     'Prod' => 'https://scramble.dedoc.co/api',
     * ],
     * ```
     */
    'servers' => null,

    /**
     * Determines how Scramble stores the descriptions of enum cases.
     * Available options:
     * - 'description' – Case descriptions are stored as the enum schema's description using table formatting.
     * - 'extension' – Case descriptions are stored in the `x-enumDescriptions` enum schema extension.
     *
     *    @see https://redocly.com/docs-legacy/api-reference-docs/specification-extensions/x-enum-descriptions
     * - false - Case descriptions are ignored.
     */
    'enum_cases_description_strategy' => 'description',

    /**
     * Determines how Scramble stores the names of enum cases.
     * Available options:
     * - 'names' – Case names are stored in the `x-enumNames` enum schema extension.
     * - 'varnames' - Case names are stored in the `x-enum-varnames` enum schema extension.
     * - false - Case names are not stored.
     */
    'enum_cases_names_strategy' => false,

    /**
     * When Scramble encounters deep objects in query parameters, it flattens the parameters so the generated
     * OpenAPI document correctly describes the API. Flattening deep query parameters is relevant until
     * OpenAPI 3.2 is released and query string structure can be described properly.
     *
     * For example, this nested validation rule describes the object with `bar` property:
     * `['foo.bar' => ['required', 'int']]`.
     *
     * When `flatten_deep_query_parameters` is `true`, Scramble will document the parameter like so:
     * `{"name":"foo[bar]", "schema":{"type":"int"}, "required":true}`.
     *
     * When `flatten_deep_query_parameters` is `false`, Scramble will document the parameter like so:
     *  `{"name":"foo", "schema": {"type":"object", "properties":{"bar":{"type": "int"}}, "required": ["bar"]}, "required":true}`.
     */
    'flatten_deep_query_parameters' => true,

    'middleware' => [
        'web',
        RestrictedDocsAccess::class,
    ],

    'extensions' => [],

    /*
     * Automatically document API security (OpenAPI `security` / `securitySchemes`) based on route
     * middleware.
     *
     * Disabled by default. Uncomment the line below to enable `MiddlewareAuthSecurityStrategy`.
     * When at least one documented route uses middleware matching the configured patterns (by default
     * `auth` and `auth:*`), bearer auth is applied globally. Routes without matching middleware are
     * marked as public (`security: []`).
     *
     * Set to `null` explicitly to disable. If you already configure security manually via
     * `afterOpenApiGenerated` / `extendOpenApi`, keep this disabled to avoid duplicate schemes.
     *
     * Customize with a class-string or [class, options]:
     *
     * 'security_strategy' => [
     *     \Dedoc\Scramble\SecurityDocumentation\MiddlewareAuthSecurityStrategy::class,
     *     [
     *         'middleware' => ['auth', 'auth:*'],
     *         'scheme' => \Dedoc\Scramble\Support\Generator\SecurityScheme::http('bearer'),
     *     ],
     * ],
     */
    // 'security_strategy' => \Dedoc\Scramble\SecurityDocumentation\MiddlewareAuthSecurityStrategy::class,
    'security_strategy' => null,
];
