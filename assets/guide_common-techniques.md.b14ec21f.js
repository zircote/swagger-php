import{_ as n,c as s,o as a,a as e}from"./app.cfc21d1f.js";const h='{"title":"Common techniques","description":"","frontmatter":{},"headers":[{"level":2,"title":"Annotation placement","slug":"annotation-placement"},{"level":2,"title":"Context awareness","slug":"context-awareness"},{"level":2,"title":"Response media type","slug":"response-media-type"},{"level":2,"title":"Using references - $ref","slug":"using-references-ref"},{"level":2,"title":"Array parameters in query","slug":"array-parameters-in-query"},{"level":2,"title":"Vendor extensions","slug":"vendor-extensions"},{"level":2,"title":"Enums","slug":"enums"},{"level":3,"title":"Enum cases as value","slug":"enum-cases-as-value"},{"level":2,"title":"Enum schemas","slug":"enum-schemas"}],"relativePath":"guide/common-techniques.md"}',t={},p=e(`<h1 id="common-techniques" tabindex="-1">Common techniques <a class="header-anchor" href="#common-techniques" aria-hidden="true">#</a></h1><h2 id="annotation-placement" tabindex="-1">Annotation placement <a class="header-anchor" href="#annotation-placement" aria-hidden="true">#</a></h2><p>You shouldn&#39;t place all annotations inside one big block, but scatter them throughout your codebase as close to the relevant source code as appropriate.</p><p><code>swagger-php</code> will scan your project and merge all meta-data into one<code> @OA\\OpenApi</code> annotation.</p><div class="warning custom-block"><p class="custom-block-title">WARNING</p><p>As of <code>swagger-php</code> v4 all annotations or attributes must be associated with a structural element (<code>class</code>, <code>method</code>, <code>parameter</code> or <code>enum</code>)</p></div><h2 id="context-awareness" tabindex="-1">Context awareness <a class="header-anchor" href="#context-awareness" aria-hidden="true">#</a></h2><p><code>swagger-php</code> looks at the context of the annotation and will augment it with things like <code>property name</code>, <code>data type</code> (doctype and native type hints) as well as a couple other things.</p><p>This means in a lot of cases it is not necessary to explicitly document all details.</p><p><strong>Example</strong></p><div class="language-php"><pre><code><span class="token php language-php"><span class="token delimiter important">&lt;?php</span>

<span class="token comment">/**
 * @OA\\Schema()
 */</span>
<span class="token keyword">class</span> <span class="token class-name-definition class-name">Product</span> <span class="token punctuation">{</span>

    <span class="token comment">/**
     * The product name,
     * @var string
     * @OA\\Property()
     */</span>
    <span class="token keyword">public</span> <span class="token variable">$name</span><span class="token punctuation">;</span>
<span class="token punctuation">}</span>
</span></code></pre></div><p><strong>Results in</strong></p><div class="language-yaml"><pre><code><span class="token key atrule">openapi</span><span class="token punctuation">:</span> 3.0.0
<span class="token key atrule">components</span><span class="token punctuation">:</span>
  <span class="token key atrule">schemas</span><span class="token punctuation">:</span>
    <span class="token key atrule">Product</span><span class="token punctuation">:</span>
      <span class="token key atrule">properties</span><span class="token punctuation">:</span>
        <span class="token key atrule">name</span><span class="token punctuation">:</span>
          <span class="token key atrule">description</span><span class="token punctuation">:</span> <span class="token string">&quot;The product name&quot;</span>
          <span class="token key atrule">type</span><span class="token punctuation">:</span> string
      <span class="token key atrule">type</span><span class="token punctuation">:</span> object
</code></pre></div><p><strong>As if you&#39;d written</strong></p><div class="language-php"><pre><code>    <span class="token comment">/**
     * The product name
     * @var string
     *
     * @OA\\Property(
     *   property=&quot;name&quot;,
     *   type=&quot;string&quot;,
     *   description=&quot;The product name&quot;
     * )
     */</span>
    <span class="token keyword">public</span> <span class="token variable">$name</span><span class="token punctuation">;</span>
</code></pre></div><h2 id="response-media-type" tabindex="-1">Response media type <a class="header-anchor" href="#response-media-type" aria-hidden="true">#</a></h2><p>The <code>@OA\\MediaType</code> is used to describe the content:</p><div class="language-php"><pre><code><span class="token comment">/**
 * @OA\\Response(
 *     response=200,
 *     description=&quot;successful operation&quot;,
 *     @OA\\MediaType(
 *         mediaType=&quot;application/json&quot;,
 *         @OA\\Schema(ref=&quot;#/components/schemas/User&quot;),
 *     )
 * ),
 */</span>
</code></pre></div><p>But because most API requests and responses are JSON, the <code>@OA\\JsonContent</code> allows you to simplify this by writing:</p><div class="language-php"><pre><code><span class="token comment">/**
 * @OA\\Response(
 *     response=200,
 *     description=&quot;successful operation&quot;,
 *     @OA\\JsonContent(ref=&quot;#/components/schemas/User&quot;),
 * )
 */</span>
</code></pre></div><p>During processing the <code>@OA\\JsonContent</code> unfolds to <code>@OA\\MediaType( mediaType=&quot;application/json&quot;, @OA\\Schema(...)</code> and will generate the same output.</p><h2 id="using-references-ref" tabindex="-1">Using references - <code>$ref</code> <a class="header-anchor" href="#using-references-ref" aria-hidden="true">#</a></h2><p>It&#39;s quite common that endpoints have some overlap in either their request or response data. To keep things DRY (Don&#39;t Repeat Yourself) the specification allows reusing components using <code>$ref</code>&#39;s</p><div class="language-php"><pre><code><span class="token comment">/**
 * @OA\\Schema(
 *   schema=&quot;product_id&quot;,
 *   type=&quot;integer&quot;,
 *   format=&quot;int64&quot;,
 *   description=&quot;The unique identifier of a product in our catalog&quot;
 * )
 */</span>
</code></pre></div><p><strong>Results in:</strong></p><div class="language-yaml"><pre><code><span class="token key atrule">openapi</span><span class="token punctuation">:</span> 3.0.0
<span class="token key atrule">components</span><span class="token punctuation">:</span>
  <span class="token key atrule">schemas</span><span class="token punctuation">:</span>
    <span class="token key atrule">product_id</span><span class="token punctuation">:</span>
      <span class="token key atrule">description</span><span class="token punctuation">:</span> <span class="token string">&quot;The unique identifier of a product in our catalog&quot;</span>
      <span class="token key atrule">type</span><span class="token punctuation">:</span> integer
      <span class="token key atrule">format</span><span class="token punctuation">:</span> int64
</code></pre></div><p>This doesn&#39;t do anything by itself, but now you can reference this fragment by its path in the document tree <code>#/components/schemas/product_id</code></p><div class="language-php"><pre><code>    <span class="token comment">/**
     * @OA\\Property(ref=&quot;#/components/schemas/product_id&quot;)
     */</span>
    <span class="token keyword">public</span> <span class="token variable">$id</span><span class="token punctuation">;</span>
</code></pre></div><div class="info custom-block"><p class="custom-block-title">Examples</p><p>There are more uses cases on how to use refs in the <a href="https://github.com/zircote/swagger-php/tree/master/Examples/using-refs" target="_blank" rel="noopener noreferrer">using-refs example</a>.</p></div><h2 id="array-parameters-in-query" tabindex="-1">Array parameters in query <a class="header-anchor" href="#array-parameters-in-query" aria-hidden="true">#</a></h2><p>Depending on <a href="https://swagger.io/specification/#style-values" target="_blank" rel="noopener noreferrer">style-values</a> <code>@OA\\Parameter(in=&quot;query&quot;, name=&quot;param&quot;, ...)</code> might create urls like this: <code>path?param=123&amp;param=abc</code> which do not work when reading the param values in PHP.</p><p>The solution is to change the name <code>param</code> into <code>param[]</code> which will create <code>path?param[]=123&amp;param[]=abc</code> and results in a PHP array.</p><h2 id="vendor-extensions" tabindex="-1">Vendor extensions <a class="header-anchor" href="#vendor-extensions" aria-hidden="true">#</a></h2><p>The specification allows for <a href="http://swagger.io/specification/#vendorExtensions" target="_blank" rel="noopener noreferrer">custom properties</a> as long as they start with &quot;x-&quot;. Therefore, all swagger-php annotations have an <code>x</code> property which accepts an array (map) and will unfold into &quot;x-&quot; properties.</p><div class="language-php"><pre><code><span class="token comment">/**
 * @OA\\Info(
 *   title=&quot;Example&quot;,
 *   version=&quot;1.0.0&quot;,
 *   x={
 *     &quot;some-name&quot;: &quot;a-value&quot;,
 *     &quot;another&quot;: 2,
 *     &quot;complex-type&quot;: {
 *       &quot;supported&quot;:{
 *         {&quot;version&quot;: &quot;1.0&quot;, &quot;level&quot;: &quot;baseapi&quot;},
 *         {&quot;version&quot;: &quot;2.1&quot;, &quot;level&quot;: &quot;fullapi&quot;},
 *       }
 *     }
 *   }
 * )
 */</span>
</code></pre></div><p><strong>Results in:</strong></p><div class="language-yaml"><pre><code><span class="token key atrule">openapi</span><span class="token punctuation">:</span> 3.0.0
<span class="token key atrule">info</span><span class="token punctuation">:</span>
  <span class="token key atrule">title</span><span class="token punctuation">:</span> Example
  <span class="token key atrule">version</span><span class="token punctuation">:</span> <span class="token number">1</span>
  <span class="token key atrule">x-some-name</span><span class="token punctuation">:</span> a<span class="token punctuation">-</span>value
  <span class="token key atrule">x-another</span><span class="token punctuation">:</span> <span class="token number">2</span>
  <span class="token key atrule">x-complex-type</span><span class="token punctuation">:</span>
    <span class="token key atrule">supported</span><span class="token punctuation">:</span>
      <span class="token punctuation">-</span> <span class="token key atrule">version</span><span class="token punctuation">:</span> <span class="token string">&quot;1.0&quot;</span>
        <span class="token key atrule">level</span><span class="token punctuation">:</span> baseapi
      <span class="token punctuation">-</span> <span class="token key atrule">version</span><span class="token punctuation">:</span> <span class="token string">&quot;2.1&quot;</span>
        <span class="token key atrule">level</span><span class="token punctuation">:</span> fullapi
</code></pre></div><h2 id="enums" tabindex="-1">Enums <a class="header-anchor" href="#enums" aria-hidden="true">#</a></h2><p><a href="https://www.php.net/manual/en/language.enumerations.basics.php" target="_blank" rel="noopener noreferrer">PHP 8.1 enums</a> are supported in two main use cases.</p><h3 id="enum-cases-as-value" tabindex="-1">Enum cases as value <a class="header-anchor" href="#enum-cases-as-value" aria-hidden="true">#</a></h3><p>Enum cases can be used as value in an <code>enum</code> list just like a <code>string</code>, <code>integer</code> or any other primitive type.</p><p><strong>Basic enum:</strong></p><div class="language-php"><pre><code><span class="token keyword">use</span> <span class="token package">OpenApi<span class="token punctuation">\\</span>Attributes</span> <span class="token keyword">as</span> <span class="token constant">OAT</span><span class="token punctuation">;</span>

<span class="token keyword">enum</span> <span class="token class-name-definition class-name">Suit</span>
<span class="token punctuation">{</span>
    <span class="token keyword">case</span> <span class="token constant">Hearts</span><span class="token punctuation">;</span>
    <span class="token keyword">case</span> <span class="token constant">Diamonds</span><span class="token punctuation">;</span>
    <span class="token keyword">case</span> <span class="token constant">Clubs</span><span class="token punctuation">;</span>
    <span class="token keyword">case</span> <span class="token constant">Spades</span><span class="token punctuation">;</span>
<span class="token punctuation">}</span>

<span class="token keyword">class</span> <span class="token class-name-definition class-name">Model</span>
<span class="token punctuation">{</span>
    <span class="token attribute"><span class="token delimiter punctuation">#[</span><span class="token attribute-content"><span class="token attribute-class-name class-name class-name-fully-qualified">OAT<span class="token punctuation">\\</span>Property</span><span class="token punctuation">(</span><span class="token attribute-class-name class-name">enum</span><span class="token punctuation">:</span> <span class="token punctuation">[</span><span class="token attribute-class-name class-name">Suit</span><span class="token operator">::</span><span class="token constant">Hearts</span><span class="token punctuation">,</span> <span class="token attribute-class-name class-name">Suit</span><span class="token operator">::</span><span class="token constant">Diamonds</span><span class="token punctuation">]</span><span class="token punctuation">)</span></span><span class="token delimiter punctuation">]</span></span>
    <span class="token keyword">protected</span> <span class="token keyword type-declaration">array</span> <span class="token variable">$someSuits</span><span class="token punctuation">;</span>
<span class="token punctuation">}</span>
</code></pre></div><p><strong>Results in:</strong></p><div class="language-yaml"><pre><code><span class="token key atrule">openapi</span><span class="token punctuation">:</span> 3.0.0
<span class="token key atrule">components</span><span class="token punctuation">:</span>
  <span class="token key atrule">schemas</span><span class="token punctuation">:</span>
    <span class="token key atrule">Model</span><span class="token punctuation">:</span>
      <span class="token key atrule">properties</span><span class="token punctuation">:</span>
        <span class="token key atrule">someSuits</span><span class="token punctuation">:</span>
          <span class="token key atrule">type</span><span class="token punctuation">:</span> array
          <span class="token key atrule">enum</span><span class="token punctuation">:</span>
            <span class="token punctuation">-</span> Hearts
            <span class="token punctuation">-</span> Diamonds
      <span class="token key atrule">type</span><span class="token punctuation">:</span> object

</code></pre></div><p><strong>Backed enums</strong></p><p>If the enum is a backed enum, the case backing value is used instead of the name.</p><h2 id="enum-schemas" tabindex="-1">Enum schemas <a class="header-anchor" href="#enum-schemas" aria-hidden="true">#</a></h2><p>The simples way of using enums is to annotate them as <code>Schema</code>. This allows you to reference them like any other schema in your spec.</p><div class="language-php"><pre><code><span class="token keyword">use</span> <span class="token package">OpenApi<span class="token punctuation">\\</span>Attributes</span> <span class="token keyword">as</span> <span class="token constant">OAT</span><span class="token punctuation">;</span>

<span class="token attribute"><span class="token delimiter punctuation">#[</span><span class="token attribute-content"><span class="token attribute-class-name class-name class-name-fully-qualified">OAT<span class="token punctuation">\\</span>Schema</span><span class="token punctuation">(</span><span class="token punctuation">)</span></span><span class="token delimiter punctuation">]</span></span>
<span class="token keyword">enum</span> <span class="token class-name-definition class-name">Colour</span>
<span class="token punctuation">{</span>
    <span class="token keyword">case</span> <span class="token constant">GREEN</span><span class="token punctuation">;</span>
    <span class="token keyword">case</span> <span class="token constant">BLUE</span><span class="token punctuation">;</span>
    <span class="token keyword">case</span> <span class="token constant">RED</span><span class="token punctuation">;</span>
<span class="token punctuation">}</span>

<span class="token attribute"><span class="token delimiter punctuation">#[</span><span class="token attribute-content"><span class="token attribute-class-name class-name class-name-fully-qualified">OAT<span class="token punctuation">\\</span>Schema</span><span class="token punctuation">(</span><span class="token punctuation">)</span></span><span class="token delimiter punctuation">]</span></span>
<span class="token keyword">class</span> <span class="token class-name-definition class-name">Product</span>
<span class="token punctuation">{</span>
    <span class="token attribute"><span class="token delimiter punctuation">#[</span><span class="token attribute-content"><span class="token attribute-class-name class-name class-name-fully-qualified">OAT<span class="token punctuation">\\</span>Property</span><span class="token punctuation">(</span><span class="token punctuation">)</span></span><span class="token delimiter punctuation">]</span></span>
    <span class="token keyword">public</span> <span class="token class-name type-declaration">Colour</span> <span class="token variable">$colour</span><span class="token punctuation">;</span>
<span class="token punctuation">}</span>
</code></pre></div><p><strong>Results in:</strong></p><div class="language-yaml"><pre><code><span class="token key atrule">openapi</span><span class="token punctuation">:</span> 3.0.0
<span class="token key atrule">components</span><span class="token punctuation">:</span>
  <span class="token key atrule">schemas</span><span class="token punctuation">:</span>
    <span class="token key atrule">Colour</span><span class="token punctuation">:</span>
      <span class="token key atrule">type</span><span class="token punctuation">:</span> string
      <span class="token key atrule">enum</span><span class="token punctuation">:</span>
        <span class="token punctuation">-</span> GREEN
        <span class="token punctuation">-</span> BLUE
        <span class="token punctuation">-</span> RED
    <span class="token key atrule">Product</span><span class="token punctuation">:</span>
      <span class="token key atrule">properties</span><span class="token punctuation">:</span>
        <span class="token key atrule">colour</span><span class="token punctuation">:</span>
          <span class="token key atrule">$ref</span><span class="token punctuation">:</span> <span class="token string">&#39;#/components/schemas/Colour&#39;</span>
      <span class="token key atrule">type</span><span class="token punctuation">:</span> object
</code></pre></div><p><strong>Backed enums</strong></p><p>For backed enums there exist two rules that determine whether the name or backing value is used:</p><ol><li>If no schema type is given, the enum name is used.</li><li>If a schema type is given, and it matches the backing type, the enum backing value is used.</li></ol><p><strong>Using the name of a backed enum:</strong></p><div class="language-php"><pre><code><span class="token keyword">use</span> <span class="token package">OpenApi<span class="token punctuation">\\</span>Attributes</span> <span class="token keyword">as</span> <span class="token constant">OAT</span><span class="token punctuation">;</span>

<span class="token attribute"><span class="token delimiter punctuation">#[</span><span class="token attribute-content"><span class="token attribute-class-name class-name class-name-fully-qualified">OAT<span class="token punctuation">\\</span>Schema</span><span class="token punctuation">(</span><span class="token punctuation">)</span></span><span class="token delimiter punctuation">]</span></span>
<span class="token keyword">enum</span> <span class="token class-name-definition class-name">Colour</span><span class="token punctuation">:</span> <span class="token keyword type-declaration">int</span>
<span class="token punctuation">{</span>
    <span class="token keyword">case</span> <span class="token constant">GREEN</span> <span class="token operator">=</span> <span class="token number">1</span><span class="token punctuation">;</span>
    <span class="token keyword">case</span> <span class="token constant">BLUE</span> <span class="token operator">=</span> <span class="token number">2</span><span class="token punctuation">;</span>
    <span class="token keyword">case</span> <span class="token constant">RED</span> <span class="token operator">=</span> <span class="token number">3</span><span class="token punctuation">;</span>
<span class="token punctuation">}</span>

<span class="token attribute"><span class="token delimiter punctuation">#[</span><span class="token attribute-content"><span class="token attribute-class-name class-name class-name-fully-qualified">OAT<span class="token punctuation">\\</span>Schema</span><span class="token punctuation">(</span><span class="token punctuation">)</span></span><span class="token delimiter punctuation">]</span></span>
<span class="token keyword">class</span> <span class="token class-name-definition class-name">Product</span>
<span class="token punctuation">{</span>
    <span class="token attribute"><span class="token delimiter punctuation">#[</span><span class="token attribute-content"><span class="token attribute-class-name class-name class-name-fully-qualified">OAT<span class="token punctuation">\\</span>Property</span><span class="token punctuation">(</span><span class="token punctuation">)</span></span><span class="token delimiter punctuation">]</span></span>
    <span class="token keyword">public</span> <span class="token class-name type-declaration">Colour</span> <span class="token variable">$colour</span><span class="token punctuation">;</span>
<span class="token punctuation">}</span>
</code></pre></div><p><strong>Results in:</strong></p><div class="language-yaml"><pre><code><span class="token key atrule">openapi</span><span class="token punctuation">:</span> 3.0.0
<span class="token key atrule">components</span><span class="token punctuation">:</span>
  <span class="token key atrule">schemas</span><span class="token punctuation">:</span>
    <span class="token key atrule">Colour</span><span class="token punctuation">:</span>
      <span class="token key atrule">type</span><span class="token punctuation">:</span> string
      <span class="token key atrule">enum</span><span class="token punctuation">:</span>
        <span class="token punctuation">-</span> GREEN
        <span class="token punctuation">-</span> BLUE
        <span class="token punctuation">-</span> RED
    <span class="token key atrule">Product</span><span class="token punctuation">:</span>
      <span class="token key atrule">properties</span><span class="token punctuation">:</span>
        <span class="token key atrule">colour</span><span class="token punctuation">:</span>
          <span class="token key atrule">$ref</span><span class="token punctuation">:</span> <span class="token string">&#39;#/components/schemas/Colour&#39;</span>
      <span class="token key atrule">type</span><span class="token punctuation">:</span> object
</code></pre></div><p><strong>Using the backing value:</strong></p><div class="language-php"><pre><code><span class="token keyword">use</span> <span class="token package">OpenApi<span class="token punctuation">\\</span>Attributes</span> <span class="token keyword">as</span> <span class="token constant">OAT</span><span class="token punctuation">;</span>

<span class="token attribute"><span class="token delimiter punctuation">#[</span><span class="token attribute-content"><span class="token attribute-class-name class-name class-name-fully-qualified">OAT<span class="token punctuation">\\</span>Schema</span><span class="token punctuation">(</span><span class="token attribute-class-name class-name">type</span><span class="token punctuation">:</span> <span class="token string single-quoted-string">&#39;integer&#39;</span><span class="token punctuation">)</span></span><span class="token delimiter punctuation">]</span></span>
<span class="token keyword">enum</span> <span class="token class-name-definition class-name">Colour</span><span class="token punctuation">:</span> <span class="token keyword type-declaration">int</span>
<span class="token punctuation">{</span>
    <span class="token keyword">case</span> <span class="token constant">GREEN</span> <span class="token operator">=</span> <span class="token number">1</span><span class="token punctuation">;</span>
    <span class="token keyword">case</span> <span class="token constant">BLUE</span> <span class="token operator">=</span> <span class="token number">2</span><span class="token punctuation">;</span>
    <span class="token keyword">case</span> <span class="token constant">RED</span> <span class="token operator">=</span> <span class="token number">3</span><span class="token punctuation">;</span>
<span class="token punctuation">}</span>

<span class="token attribute"><span class="token delimiter punctuation">#[</span><span class="token attribute-content"><span class="token attribute-class-name class-name class-name-fully-qualified">OAT<span class="token punctuation">\\</span>Schema</span><span class="token punctuation">(</span><span class="token punctuation">)</span></span><span class="token delimiter punctuation">]</span></span>
<span class="token keyword">class</span> <span class="token class-name-definition class-name">Product</span>
<span class="token punctuation">{</span>
    <span class="token attribute"><span class="token delimiter punctuation">#[</span><span class="token attribute-content"><span class="token attribute-class-name class-name class-name-fully-qualified">OAT<span class="token punctuation">\\</span>Property</span><span class="token punctuation">(</span><span class="token punctuation">)</span></span><span class="token delimiter punctuation">]</span></span>
    <span class="token keyword">public</span> <span class="token class-name type-declaration">Colour</span> <span class="token variable">$colour</span><span class="token punctuation">;</span>
<span class="token punctuation">}</span>
</code></pre></div><p><strong>Results in:</strong></p><div class="language-yaml"><pre><code><span class="token key atrule">openapi</span><span class="token punctuation">:</span> 3.0.0
<span class="token key atrule">components</span><span class="token punctuation">:</span>
  <span class="token key atrule">schemas</span><span class="token punctuation">:</span>
    <span class="token key atrule">Colour</span><span class="token punctuation">:</span>
      <span class="token key atrule">type</span><span class="token punctuation">:</span> integer
      <span class="token key atrule">enum</span><span class="token punctuation">:</span>
        <span class="token punctuation">-</span> <span class="token number">1</span>
        <span class="token punctuation">-</span> <span class="token number">2</span>
        <span class="token punctuation">-</span> <span class="token number">3</span>
    <span class="token key atrule">Product</span><span class="token punctuation">:</span>
      <span class="token key atrule">properties</span><span class="token punctuation">:</span>
        <span class="token key atrule">colour</span><span class="token punctuation">:</span>
          <span class="token key atrule">$ref</span><span class="token punctuation">:</span> <span class="token string">&#39;#/components/schemas/Colour&#39;</span>
      <span class="token key atrule">type</span><span class="token punctuation">:</span> object
</code></pre></div>`,62),o=[p];function c(l,u,i,r,k,d){return a(),s("div",null,o)}var y=n(t,[["render",c]]);export{h as __pageData,y as default};
