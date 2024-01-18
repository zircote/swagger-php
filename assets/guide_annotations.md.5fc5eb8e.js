import{_ as n,c as s,o as a,a as t}from"./app.cfc21d1f.js";const q='{"title":"Annotations","description":"","frontmatter":{},"headers":[{"level":2,"title":"Doctrine","slug":"doctrine"},{"level":2,"title":"Escaping","slug":"escaping"},{"level":2,"title":"Arrays and Objects","slug":"arrays-and-objects"},{"level":2,"title":"Constants","slug":"constants"}],"relativePath":"guide/annotations.md"}',o={},e=t(`<h1 id="annotations" tabindex="-1">Annotations <a class="header-anchor" href="#annotations" aria-hidden="true">#</a></h1><div class="tip custom-block"><p class="custom-block-title">Namespace</p><p>Using a namespace alias simplifies typing and improves readability.</p><p>All annotations are in the <code>OpenApi\\Annotations</code> namespace.</p></div><p>Since Annotations are technically PHP comments, adding <code>use OpenApi\\Annotations as OA;</code> is strictly speaking not necessary. However, doctrine will be quite specific about whether an alias is valid or not.</p><p><code>swagger-php</code> will automatically register the <code>@OA</code> alias so all annotations can be used using the <code>@OA</code> shortcut without any additional work.</p><h2 id="doctrine" tabindex="-1">Doctrine <a class="header-anchor" href="#doctrine" aria-hidden="true">#</a></h2><p>Annotations are PHP comments (docblocks) containing <a href="https://www.doctrine-project.org/projects/annotations.html" target="_blank" rel="noopener noreferrer">doctrine style annotations</a>.</p><div class="info custom-block"><p class="custom-block-title">INFO</p><p>All documentation related to doctrine applies to annotations only.</p></div><p><strong>Example:</strong></p><div class="language-php"><pre><code><span class="token php language-php"><span class="token delimiter important">&lt;?php</span>

<span class="token keyword">use</span> <span class="token package">OpenApi<span class="token punctuation">\\</span>Annotations</span> <span class="token keyword">as</span> <span class="token constant">OA</span><span class="token punctuation">;</span>

<span class="token comment">/**
 * @OA\\Info(title=&quot;My First API&quot;, version=&quot;0.1&quot;)
 */</span>
<span class="token keyword">class</span> <span class="token class-name-definition class-name">OpenApi</span> <span class="token punctuation">{</span><span class="token punctuation">}</span>

<span class="token keyword">class</span> <span class="token class-name-definition class-name">MyController</span> <span class="token punctuation">{</span>

    <span class="token comment">/**
     * @OA\\Get(
     *     path=&quot;/api/resource.json&quot;,
     *     @OA\\Response(response=&quot;200&quot;, description=&quot;An example resource&quot;)
     * )
     */</span>
    <span class="token keyword">public</span> <span class="token keyword">function</span> <span class="token function-definition function">resource</span><span class="token punctuation">(</span><span class="token punctuation">)</span> <span class="token punctuation">{</span>
        <span class="token comment">// ...</span>
    <span class="token punctuation">}</span>
<span class="token punctuation">}</span>
</span></code></pre></div><h2 id="escaping" tabindex="-1">Escaping <a class="header-anchor" href="#escaping" aria-hidden="true">#</a></h2><div class="tip custom-block"><p class="custom-block-title">Escaping double quotes</p><p>Double quotes can be escaped by doubling them rather than using <code>\\</code></p><p>For example:</p><div class="language-php"><pre><code>    @<span class="token constant">OA</span><span class="token function"><span class="token punctuation">\\</span>Schema</span><span class="token punctuation">(</span>
       title<span class="token operator">=</span><span class="token string double-quoted-string">&quot;Request&quot;</span><span class="token punctuation">,</span>
       schema<span class="token operator">=</span><span class="token string double-quoted-string">&quot;Request&quot;</span><span class="token punctuation">,</span>
       example<span class="token operator">=</span><span class="token punctuation">{</span>
          <span class="token string double-quoted-string">&quot;configuration&quot;</span><span class="token punctuation">:</span><span class="token string double-quoted-string">&quot;{&quot;</span><span class="token string double-quoted-string">&quot;formConfig&quot;</span><span class="token string double-quoted-string">&quot;:123}&quot;</span>
       <span class="token punctuation">}</span>
     <span class="token punctuation">)</span>
</code></pre></div></div><h2 id="arrays-and-objects" tabindex="-1">Arrays and Objects <a class="header-anchor" href="#arrays-and-objects" aria-hidden="true">#</a></h2><p>Doctrine annotations support arrays, but use <code>{</code> and <code>}</code> instead of <code>[</code> and <code>]</code>.</p><p>Doctrine also supports objects, which also use <code>{</code> and <code>}</code> and require the property names to be surrounded with <code>&quot;</code>.</p><div class="warning custom-block"><p class="custom-block-title">DON&#39;T WRITE</p><div class="language-php"><pre><code><span class="token comment">/**
 * @OA\\Info(
 *   title=&quot;My first API&quot;,
 *   version=&quot;1.0.0&quot;,
 *   contact={
 *     &quot;email&quot;: &quot;support@example.com&quot;
 *   }
 * )
 */</span>
</code></pre></div></div><p>This &quot;works&quot; but most objects have an annotation with the same name as the property, such as <code>@OA\\Contact</code> for <code>contact</code>:</p><div class="tip custom-block"><p class="custom-block-title">WRITE</p><div class="language-php"><pre><code><span class="token comment">/**
 * @OA\\Info(
 *   title=&quot;My first API&quot;,
 *   version=&quot;1.0.0&quot;,
 *   @OA\\Contact(
 *     email=&quot;support@example.com&quot;
 *   )
 * )
 */</span>
</code></pre></div></div><p>This also adds validation, so when you misspell a property or forget a required property, it will trigger a PHP warning.</p><p>For example, if you write <code>emial=&quot;support@example.com&quot;</code>, swagger-php would generate a notice with <code>Unexpected field &quot;emial&quot; for @OA\\Contact(), expecting &quot;name&quot;, &quot;email&quot;, ...</code></p><p>Placing multiple annotations of the same type will result in an array of objects. For objects, the key is defined by the field with the same name as the annotation: <code>response</code> in a <code>@OA\\Response</code>, <code>property</code> in a <code>@OA\\Property</code>, etc.</p><div class="language-php"><pre><code><span class="token comment">/**
 * @OA\\Get(
 *   path=&quot;/products&quot;,
 *   summary=&quot;list products&quot;,
 *   @OA\\Response(
 *     response=200,
 *     description=&quot;A list with products&quot;
 *   ),
 *   @OA\\Response(
 *     response=&quot;default&quot;,
 *     description=&quot;an &quot;&quot;unexpected&quot;&quot; error&quot;
 *   )
 * )
 */</span>
</code></pre></div><p><strong>Results in</strong></p><div class="language-yaml"><pre><code><span class="token key atrule">openapi</span><span class="token punctuation">:</span> 3.0.0
<span class="token key atrule">paths</span><span class="token punctuation">:</span>
  <span class="token key atrule">/products</span><span class="token punctuation">:</span>
    <span class="token key atrule">get</span><span class="token punctuation">:</span>
      <span class="token key atrule">summary</span><span class="token punctuation">:</span> <span class="token string">&quot;list products&quot;</span>
      <span class="token key atrule">responses</span><span class="token punctuation">:</span>
        <span class="token key atrule">&quot;200&quot;</span><span class="token punctuation">:</span>
          <span class="token key atrule">description</span><span class="token punctuation">:</span> <span class="token string">&quot;A list with products&quot;</span>
        <span class="token key atrule">default</span><span class="token punctuation">:</span>
          <span class="token key atrule">description</span><span class="token punctuation">:</span> <span class="token string">&#39;an &quot;unexpected&quot; error&#39;</span>
</code></pre></div><h2 id="constants" tabindex="-1">Constants <a class="header-anchor" href="#constants" aria-hidden="true">#</a></h2><p>You can use constants inside doctrine annotations.</p><div class="language-php"><pre><code><span class="token function">define</span><span class="token punctuation">(</span><span class="token string double-quoted-string">&quot;API_HOST&quot;</span><span class="token punctuation">,</span> <span class="token punctuation">(</span><span class="token variable">$env</span> <span class="token operator">===</span> <span class="token string double-quoted-string">&quot;production&quot;</span><span class="token punctuation">)</span> <span class="token operator">?</span> <span class="token string double-quoted-string">&quot;example.com&quot;</span> <span class="token punctuation">:</span> <span class="token string double-quoted-string">&quot;localhost&quot;</span><span class="token punctuation">)</span><span class="token punctuation">;</span>
</code></pre></div><div class="language-php"><pre><code><span class="token comment">/**
 * @OA\\Server(url=API_HOST)
 */</span>
</code></pre></div><div class="tip custom-block"><p class="custom-block-title">TIP</p><p>Using the CLI you might need to include the php file with the constants using the <code>--bootstrap</code> options.</p><div class="language-shell"><pre><code><span class="token operator">&gt;</span> openapi <span class="token parameter variable">--bootstrap</span> constants.php
</code></pre></div></div>`,28),p=[e];function c(i,l,u,r,d,k){return a(),s("div",null,p)}var g=n(o,[["render",c]]);export{q as __pageData,g as default};
