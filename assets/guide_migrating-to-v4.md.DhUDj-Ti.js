import{_ as a,c as i,o as e,aj as t}from"./chunks/framework.BHJ4BWLg.js";const c=JSON.parse('{"title":"Migrating to v4","description":"","frontmatter":{},"headers":[],"relativePath":"guide/migrating-to-v4.md","filePath":"guide/migrating-to-v4.md"}'),n={name:"guide/migrating-to-v4.md"};function l(h,s,p,r,o,k){return e(),i("div",null,s[0]||(s[0]=[t(`<h1 id="migrating-to-v4" tabindex="-1">Migrating to v4 <a class="header-anchor" href="#migrating-to-v4" aria-label="Permalink to &quot;Migrating to v4&quot;">​</a></h1><h2 id="overview" tabindex="-1">Overview <a class="header-anchor" href="#overview" aria-label="Permalink to &quot;Overview&quot;">​</a></h2><ul><li>As of PHP 8.1 annotations may be used as <a href="https://www.php.net/manual/en/language.attributes.overview.php" target="_blank" rel="noreferrer">PHP attributes</a> instead. That means all references to annotations in this document also apply to attributes.</li><li>If you haven&#39;t switched to attributes yet, the Doctrine annotations library must be installed manually: <code>composer require doctrine/annotations</code></li><li>Annotations now <strong>must be</strong> associated with a structural element (class, trait, interface), a method, property or const.</li><li>A new annotation <code>PathParameter</code> was added for improved framework support.</li><li>A new annotation <code>Attachable</code> was added to simplify custom processing. <code>Attachable</code> can be used to attach arbitrary data to any given annotation.</li><li>Deprecated elements have been removed <ul><li><code>\\Openapi\\Analysis::processors()</code></li><li><code>\\Openapi\\Analyser::$whitelist</code></li><li><code>\\Openapi\\Analyser::$defaultImports</code></li><li><code>\\Openapi\\Logger</code></li></ul></li><li>Legacy support is available via the previous <code>TokenAnalyser</code></li><li>Improvements to the <code>Generator</code> class</li></ul><h2 id="annotations-as-php-attributes" tabindex="-1">Annotations as PHP attributes <a class="header-anchor" href="#annotations-as-php-attributes" aria-label="Permalink to &quot;Annotations as PHP attributes&quot;">​</a></h2><p>While PHP attributes have been around since PHP 8.0 they were lacking the ability to be nested. This changes with PHP 8.1 which allows to use <code>new</code> in initializers.</p><p>Swagger-php attributes also make use of named arguments, so attribute parameters can be (mostly) typed. There are some limitations to type hints which can only be resolved once support for PHP 7.x is dropped.</p><h3 id="using-annotations" tabindex="-1">Using annotations <a class="header-anchor" href="#using-annotations" aria-label="Permalink to &quot;Using annotations&quot;">​</a></h3><div class="language-php vp-adaptive-theme"><button title="Copy Code" class="copy"></button><span class="lang">php</span><pre class="shiki shiki-themes github-light github-dark vp-code" tabindex="0"><code><span class="line"></span>
<span class="line"><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">use</span><span style="--shiki-light:#005CC5;--shiki-dark:#79B8FF;"> OpenApi\\Annotations</span><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;"> as</span><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;"> OA</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">;</span></span>
<span class="line"></span>
<span class="line"><span style="--shiki-light:#6A737D;--shiki-dark:#6A737D;">/**</span></span>
<span class="line"><span style="--shiki-light:#6A737D;--shiki-dark:#6A737D;"> * @OA\\Info(</span></span>
<span class="line"><span style="--shiki-light:#6A737D;--shiki-dark:#6A737D;"> *   version=&quot;1.0.0&quot;,</span></span>
<span class="line"><span style="--shiki-light:#6A737D;--shiki-dark:#6A737D;"> *   title=&quot;My API&quot;,</span></span>
<span class="line"><span style="--shiki-light:#6A737D;--shiki-dark:#6A737D;"> *   @OA\\License(name=&quot;MIT&quot;),</span></span>
<span class="line"><span style="--shiki-light:#6A737D;--shiki-dark:#6A737D;"> *   @OA\\Attachable()</span></span>
<span class="line"><span style="--shiki-light:#6A737D;--shiki-dark:#6A737D;"> * )</span></span>
<span class="line"><span style="--shiki-light:#6A737D;--shiki-dark:#6A737D;"> */</span></span>
<span class="line"><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">class</span><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;"> OpenApiSpec</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">{</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">}</span></span></code></pre></div><h3 id="using-attributes" tabindex="-1">Using attributes <a class="header-anchor" href="#using-attributes" aria-label="Permalink to &quot;Using attributes&quot;">​</a></h3><div class="language-php vp-adaptive-theme"><button title="Copy Code" class="copy"></button><span class="lang">php</span><pre class="shiki shiki-themes github-light github-dark vp-code" tabindex="0"><code><span class="line"></span>
<span class="line"><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">use</span><span style="--shiki-light:#005CC5;--shiki-dark:#79B8FF;"> OpenApi\\Attributes</span><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;"> as</span><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;"> OA</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">;</span></span>
<span class="line"></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">#[</span><span style="--shiki-light:#005CC5;--shiki-dark:#79B8FF;">OA\\Info</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">(</span></span>
<span class="line"><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">    version</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">: </span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;">&#39;1.0.0&#39;</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">,</span></span>
<span class="line"><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">    title</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">: </span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;">&#39;My API&#39;</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">,</span></span>
<span class="line"><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">    attachables</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">: [</span><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">new</span><span style="--shiki-light:#005CC5;--shiki-dark:#79B8FF;"> OA\\Attachable</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">()]</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">)]</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">#[</span><span style="--shiki-light:#005CC5;--shiki-dark:#79B8FF;">OA\\License</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">(</span><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">name</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">: </span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;">&#39;MIT&#39;</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">)]</span></span>
<span class="line"><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">class</span><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;"> OpenApiSpec</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">{</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">}</span></span></code></pre></div><h2 id="optional-nesting" tabindex="-1">Optional nesting <a class="header-anchor" href="#optional-nesting" aria-label="Permalink to &quot;Optional nesting&quot;">​</a></h2><p>One of the few differences between annotations and attributes visible in the above example is that the <code>OA\\License</code> attribute is not nested within <code>OA\\Info</code>. Nesting of attributes is possible and required in certain cases however, <strong>in cases where there is no ambiguity attributes may be all written on the top level</strong> and swagger-php will do the rest.</p><h2 id="annotations-must-be-associated-with-a-structural-element" tabindex="-1">Annotations must be associated with a structural element <a class="header-anchor" href="#annotations-must-be-associated-with-a-structural-element" aria-label="Permalink to &quot;Annotations must be associated with a structural element&quot;">​</a></h2><p>The (now legacy) way of parsing PHP files meant that docblocks could live in a file without a single line of actual PHP code.</p><p>PHP Attributes cannot exist in isolation; they need code to be associated with and then are available via reflection on the associated structural element. In order to allow to keep supporting annotations and the code simple it made sense to treat annotations and attributes the same in this respect.</p><h2 id="the-pathparameter-annotation" tabindex="-1">The <code>PathParameter</code> annotation <a class="header-anchor" href="#the-pathparameter-annotation" aria-label="Permalink to &quot;The \`PathParameter\` annotation&quot;">​</a></h2><p>As annotation this is just a short form for</p><div class="language-php vp-adaptive-theme"><button title="Copy Code" class="copy"></button><span class="lang">php</span><pre class="shiki shiki-themes github-light github-dark vp-code" tabindex="0"><code><span class="line"><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">   @</span><span style="--shiki-light:#005CC5;--shiki-dark:#79B8FF;">OA\\</span><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">Parameter</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">(</span><span style="--shiki-light:#005CC5;--shiki-dark:#79B8FF;">in</span><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">=</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;">&#39;body&#39;</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">)</span></span></code></pre></div><p>Things get more interesting when it comes to using it as attribute, though. In the context of a controller you can now do something like</p><div class="language-php vp-adaptive-theme"><button title="Copy Code" class="copy"></button><span class="lang">php</span><pre class="shiki shiki-themes github-light github-dark vp-code" tabindex="0"><code><span class="line"><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">class</span><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;"> MyController</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">{</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">    #[</span><span style="--shiki-light:#005CC5;--shiki-dark:#79B8FF;">OA\\Get</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">(</span><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">path</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">: </span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;">&#39;/products/{product_id}&#39;</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">)]</span></span>
<span class="line"><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">    public</span><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;"> function</span><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;"> getProduct</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">(</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">        #[</span><span style="--shiki-light:#005CC5;--shiki-dark:#79B8FF;">OA\\PathParameter</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">] </span><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">string</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;"> $product_id)</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">    {</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">    }</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">}</span></span></code></pre></div><p>Here it avoids having to duplicate details about the <code>$product_id</code> parameter and the simple use of the attribute will pick up typehints automatically.</p><h2 id="the-attachable-annotation" tabindex="-1">The <code>Attachable</code> annotation <a class="header-anchor" href="#the-attachable-annotation" aria-label="Permalink to &quot;The \`Attachable\` annotation&quot;">​</a></h2><p>Technically these were added in version 3.3.0, however they become really useful only with version 4.</p><p>The attachable annotation is similar to the OpenApi vendor extension <code>x=</code>. The main difference are that</p><ol><li>Attachables allow complex structures and strong typing</li><li><strong>Attachables are not added to the generated spec.</strong></li></ol><p>Their main purpose is to make customizing swagger-php easier by allowing to add arbitrary data to any annotation.</p><p>One possible use case could be custom annotations. Classes extending <code>Attachable</code> are allowed to limit the allowed parent annotations. This means it would be easy to create a new attribute to flag certain endpoints as private and exclude them under certain conditions from the spec (via a custom processor).</p><h2 id="removed-deprecated-elements" tabindex="-1">Removed deprecated elements <a class="header-anchor" href="#removed-deprecated-elements" aria-label="Permalink to &quot;Removed deprecated elements&quot;">​</a></h2><h3 id="openapi-analysis-processors" tabindex="-1"><code>\\Openapi\\Analysis::processors()</code> <a class="header-anchor" href="#openapi-analysis-processors" aria-label="Permalink to &quot;\`\\Openapi\\Analysis::processors()\`&quot;">​</a></h3><p>Processors have been moved into the <code>Generator</code> class incl. some new convenience methods.</p><h3 id="openapi-analyser-whitelist" tabindex="-1"><code>\\Openapi\\Analyser::$whitelist</code> <a class="header-anchor" href="#openapi-analyser-whitelist" aria-label="Permalink to &quot;\`\\Openapi\\Analyser::$whitelist\`&quot;">​</a></h3><p>This has been replaced with the <code>Generator</code> <code>namespaces</code> property.</p><h3 id="openapi-analyser-defaultimports" tabindex="-1"><code>\\Openapi\\Analyser::$defaultImports</code> <a class="header-anchor" href="#openapi-analyser-defaultimports" aria-label="Permalink to &quot;\`\\Openapi\\Analyser::$defaultImports\`&quot;">​</a></h3><p>This has been replaced with the <code>Generator</code> <code>aliases</code> property.</p><h3 id="openapi-logger" tabindex="-1"><code>\\Openapi\\Logger</code> <a class="header-anchor" href="#openapi-logger" aria-label="Permalink to &quot;\`\\Openapi\\Logger\`&quot;">​</a></h3><p>This class has been removed completely. Instead, you may configure a <a href="https://www.php-fig.org/psr/psr-3/" target="_blank" rel="noreferrer">PSR-3 logger</a>.</p><h2 id="improvements-to-the-generator-class" tabindex="-1">Improvements to the <code>Generator</code> class <a class="header-anchor" href="#improvements-to-the-generator-class" aria-label="Permalink to &quot;Improvements to the \`Generator\` class&quot;">​</a></h2><p>The removal of deprecated static config options means that the <code>Generator</code> class now is the main entry point into swagger-php when used programmatically.</p><p>To make the migration as simple as possible a new <code>Generator::withContext(callable)</code> has been added. This allows to use parts of the library (an <code>Analyser</code> instance, for example) within the context of a <code>Generator</code> instance.</p><p>Example:</p><div class="language-php vp-adaptive-theme"><button title="Copy Code" class="copy"></button><span class="lang">php</span><pre class="shiki shiki-themes github-light github-dark vp-code" tabindex="0"><code><span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">$analyser </span><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">=</span><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;"> createMyAnalyser</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">();</span></span>
<span class="line"></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">$analysis </span><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">=</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;"> (</span><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">new</span><span style="--shiki-light:#005CC5;--shiki-dark:#79B8FF;"> Generator</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">())</span></span>
<span class="line"><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">    -&gt;</span><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">addAlias</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">(</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;">&#39;fo&#39;</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">, </span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;">&#39;My</span><span style="--shiki-light:#005CC5;--shiki-dark:#79B8FF;">\\\\</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;">Attribute</span><span style="--shiki-light:#005CC5;--shiki-dark:#79B8FF;">\\\\</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;">Namespace&#39;</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">)</span></span>
<span class="line"><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">    -&gt;</span><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">addNamespace</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">(</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;">&#39;Other</span><span style="--shiki-light:#005CC5;--shiki-dark:#79B8FF;">\\\\</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;">Annotations</span><span style="--shiki-light:#005CC5;--shiki-dark:#79B8FF;">\\\\</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;">&#39;</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">)</span></span>
<span class="line"><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">    -&gt;</span><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">withContext</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">(</span><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">function</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;"> (</span><span style="--shiki-light:#005CC5;--shiki-dark:#79B8FF;">Generator</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;"> $generator, </span><span style="--shiki-light:#005CC5;--shiki-dark:#79B8FF;">Analysis</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;"> $analysis, </span><span style="--shiki-light:#005CC5;--shiki-dark:#79B8FF;">Context</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;"> $context) </span><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">use</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;"> ($analyser) {</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">        $analyser</span><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">-&gt;</span><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">setGenerator</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">($generator);</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">        $analysis </span><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">=</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;"> $analyser</span><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">-&gt;</span><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">fromFile</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">(</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;">&#39;my_code.php&#39;</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">, $context);</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">        $analysis</span><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">-&gt;</span><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">process</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">($generator</span><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">-&gt;</span><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">getProcessors</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">());</span></span>
<span class="line"></span>
<span class="line"><span style="--shiki-light:#D73A49;--shiki-dark:#F97583;">        return</span><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;"> $analysis;</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">    });</span></span></code></pre></div>`,41)]))}const g=a(n,[["render",l]]);export{c as __pageData,g as default};
