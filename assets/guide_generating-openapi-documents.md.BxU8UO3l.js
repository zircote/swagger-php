import{_ as i,c as a,o as n,aj as e}from"./chunks/framework.BHJ4BWLg.js";const c=JSON.parse('{"title":"Generating OpenAPI documents","description":"","frontmatter":{},"headers":[],"relativePath":"guide/generating-openapi-documents.md","filePath":"guide/generating-openapi-documents.md"}'),t={name:"guide/generating-openapi-documents.md"};function p(l,s,h,k,o,r){return n(),a("div",null,s[0]||(s[0]=[e(`<h1 id="generating-openapi-documents" tabindex="-1">Generating OpenAPI documents <a class="header-anchor" href="#generating-openapi-documents" aria-label="Permalink to &quot;Generating OpenAPI documents&quot;">​</a></h1><h2 id="vendor-bin-openapi" tabindex="-1"><code>./vendor/bin/openapi</code> <a class="header-anchor" href="#vendor-bin-openapi" aria-label="Permalink to &quot;\`./vendor/bin/openapi\`&quot;">​</a></h2><p><code>swagger-php</code> includes a command line tool <code>./vendor/bin/openapi</code>. This can be used to generate OpenAPI documents.</p><div class="language-shell vp-adaptive-theme"><button title="Copy Code" class="copy"></button><span class="lang">shell</span><pre class="shiki shiki-themes github-light github-dark vp-code" tabindex="0"><code><span class="line"><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">&gt;</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;"> ./vendor/bin/openapi app -o openapi.yaml</span></span></code></pre></div><div class="tip custom-block"><p class="custom-block-title">Output Format</p><p>By default the output format is YAML. If a filename is given (via <code>--output</code> or <code>-o</code>) the tool will use the file extension to determine the format.</p><p>The <code>--format</code> option can be used to force a specific format.</p></div><div class="tip custom-block"><p class="custom-block-title">Bootstrap</p><p>The bootstrap option <code>-b</code> is useful when trying to use <code>swagger-php</code> without proper autoloading.</p><p>For example, you might want to evaluate the library using a single file with just a few annotations. In this case telling swagger-php to bootstrap (pre-load) the file prior to processing it will ensure PHP&#39;s <code>reflection</code> code will be able to inspect your code.</p><div class="language-shell vp-adaptive-theme"><button title="Copy Code" class="copy"></button><span class="lang">shell</span><pre class="shiki shiki-themes github-light github-dark vp-code" tabindex="0"><code><span class="line"><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">&gt;</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;"> ./vendor/bin/openapi -b my_file.php my_file.php</span></span></code></pre></div></div><p>For a list of all available options use the <code>-h</code> option</p><div class="language-shell vp-adaptive-theme"><button title="Copy Code" class="copy"></button><span class="lang">shell</span><pre class="shiki shiki-themes github-light github-dark vp-code" tabindex="0"><code><span class="line"><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">&gt;</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;"> ./vendor/bin/openapi -h</span></span>
<span class="line"></span>
<span class="line"><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">Usage:</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;"> openapi</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;"> [--option </span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;">value]</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;"> [/path/to/project </span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;">...]</span></span>
<span class="line"></span>
<span class="line"><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">Options:</span></span>
<span class="line"><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">  --config</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;"> (-c)     Generator config</span></span>
<span class="line"><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">                    ex:</span><span style="--shiki-light:#005CC5;--shiki-dark:#79B8FF;"> -c</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;"> operationId.hash=</span><span style="--shiki-light:#005CC5;--shiki-dark:#79B8FF;">false</span></span>
<span class="line"><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">  --output</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;"> (-o)     Path to store the generated documentation.</span></span>
<span class="line"><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">                    ex:</span><span style="--shiki-light:#005CC5;--shiki-dark:#79B8FF;"> --output</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;"> openapi.yaml</span></span>
<span class="line"><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">  --exclude</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;"> (-e)    Exclude path(</span><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">s</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">).</span></span>
<span class="line"><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">                    ex:</span><span style="--shiki-light:#005CC5;--shiki-dark:#79B8FF;"> --exclude</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;"> vendor,library/Zend</span></span>
<span class="line"><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">  --pattern</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;"> (-n)    Pattern of files to scan.</span></span>
<span class="line"><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">                    ex:</span><span style="--shiki-light:#005CC5;--shiki-dark:#79B8FF;"> --pattern</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;"> &quot;*.php&quot;</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;"> or</span><span style="--shiki-light:#005CC5;--shiki-dark:#79B8FF;"> --pattern</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;"> &quot;/\\.(phps|php)$/&quot;</span></span>
<span class="line"><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">  --bootstrap</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;"> (-b)  Bootstrap a php file </span><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">for</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;"> defining constants, etc.</span></span>
<span class="line"><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">                    ex:</span><span style="--shiki-light:#005CC5;--shiki-dark:#79B8FF;"> --bootstrap</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;"> config/constants.php</span></span>
<span class="line"><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">  --processor</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;"> (-p)  Register an additional processor.</span></span>
<span class="line"><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">  --format</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;"> (-f)     Force yaml or json.</span></span>
<span class="line"><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">  --debug</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;"> (-d)      Show additional error information.</span></span>
<span class="line"><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">  --version</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;">         The</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;"> OpenAPI</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;"> version</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">; </span><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">defaults</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;"> to</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;"> 3.0.0.</span></span>
<span class="line"><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">  --help</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;"> (-h)       Display this help message.</span></span></code></pre></div><h2 id="using-php" tabindex="-1">Using PHP <a class="header-anchor" href="#using-php" aria-label="Permalink to &quot;Using PHP&quot;">​</a></h2><p>Depending on your use case PHP code can also be used to generate OpenAPI documents in a more dynamic way.</p><p>In its simplest form this may look something like</p><div class="language-php vp-adaptive-theme"><button title="Copy Code" class="copy"></button><span class="lang">php</span><pre class="shiki shiki-themes github-light github-dark vp-code" tabindex="0"><code><span class="line"><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">&lt;?</span><span style="--shiki-light:#005CC5;--shiki-dark:#79B8FF;">php</span></span>
<span class="line"><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">require</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">(</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;">&quot;vendor/autoload.php&quot;</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">);</span></span>
<span class="line"></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">$openapi </span><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">=</span><span style="--shiki-light:#005CC5;--shiki-dark:#79B8FF;"> \\OpenApi\\Generator</span><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">::</span><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">scan</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">([</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;">&#39;/path/to/project&#39;</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">]);</span></span>
<span class="line"></span>
<span class="line"><span style="--shiki-light:#005CC5;--shiki-dark:#79B8FF;">header</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">(</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;">&#39;Content-Type: application/x-yaml&#39;</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">);</span></span>
<span class="line"><span style="--shiki-light:#005CC5;--shiki-dark:#79B8FF;">echo</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;"> $openapi</span><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">-&gt;</span><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">toYaml</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">();</span></span></code></pre></div><div class="tip custom-block"><p class="custom-block-title">Programming API</p><p>Details about the <code>swagger-php</code> API can be found in the <a href="./../reference/">reference</a>.</p></div>`,13)]))}const g=i(t,[["render",p]]);export{c as __pageData,g as default};
