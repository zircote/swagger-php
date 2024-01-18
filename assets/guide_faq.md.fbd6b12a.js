import{_ as n,c as e,o as a,a as s}from"./app.cfc21d1f.js";const k='{"title":"FAQ","description":"","frontmatter":{},"headers":[{"level":2,"title":"Warning: Required @OA\\\\Info() not found","slug":"warning-required-oa-info-not-found"},{"level":2,"title":"Annotations missing","slug":"annotations-missing"},{"level":2,"title":"Skipping unknown \\\\SomeClass","slug":"skipping-unknown-someclass"},{"level":3,"title":"Using the -b --bootstrap option","slug":"using-the-b-bootstrap-option"},{"level":3,"title":"Namespace mismatch","slug":"namespace-mismatch"},{"level":2,"title":"No output from openapi command line tool","slug":"no-output-from-openapi-command-line-tool"}],"relativePath":"guide/faq.md"}',t={},o=s(`<h1 id="faq" tabindex="-1">FAQ <a class="header-anchor" href="#faq" aria-hidden="true">#</a></h1><h2 id="warning-required-oa-info-not-found" tabindex="-1">Warning: Required <code>@OA\\Info()</code> not found <a class="header-anchor" href="#warning-required-oa-info-not-found" aria-hidden="true">#</a></h2><p>With adding support for <a href="https://www.php.net/manual/en/language.attributes.php" target="_blank" rel="noopener noreferrer">PHP attributes</a> in version 4, some architectural changes had to be made.</p><p>One of those changes is that placing annotations in your source files is now subject to the same limitations as attributes. These limits are dictated by the PHP reflection API, specifically where it provides access to attributes and doc comments.</p><p>This means stand-alone annotations are no longer supported and ignored as <code>swagger-php</code> cannot <em>&quot;see&quot;</em> them any more.</p><p>Supported locations:</p><ul><li>class</li><li>interface</li><li>trait</li><li>method</li><li>property</li><li>class/interface const</li></ul><p>Most commonly this manifests with a warning about the required <code>@OA\\Info</code> not being found. While most annotations have specific related code, the info annotation (and a few more) is kind of global.</p><p>The simplest solution to avoid this issue is to add a &#39;dummy&#39; class to the docblock and add all &#39;global&#39; annotations (e.g. <code>Tag</code>, <code>Server</code>, <code>SecurityScheme</code>, etc.) <strong>in a single docblock</strong> to that class.</p><div class="language-php"><pre><code><span class="token comment">/**
 * @OA\\Tag(
 *     name=&quot;user&quot;,
 *     description=&quot;User related operations&quot;
 * )
 * @OA\\Info(
 *     version=&quot;1.0&quot;,
 *     title=&quot;Example API&quot;,
 *     description=&quot;Example info&quot;,
 *     @OA\\Contact(name=&quot;Swagger API Team&quot;)
 * )
 * @OA\\Server(
 *     url=&quot;https://example.localhost&quot;,
 *     description=&quot;API server&quot;
 * )
 */</span>
<span class="token keyword">class</span> <span class="token class-name-definition class-name">OpenApiSpec</span>
<span class="token punctuation">{</span>
<span class="token punctuation">}</span>
</code></pre></div><p><strong>As of version 4.8 the <code>doctrine/annotations</code> library is optional and might cause the same message.</strong></p><p>If this is the case, <code>doctrine annotations</code> must be installed separately:</p><div class="language-shell"><pre><code><span class="token function">composer</span> require doctrine/annotations
</code></pre></div><h2 id="annotations-missing" tabindex="-1">Annotations missing <a class="header-anchor" href="#annotations-missing" aria-hidden="true">#</a></h2><p>Another side effect of using reflection is that <code>swagger-php</code> <em>&quot;can&#39;t see&quot;</em> multiple consecutive docblocks any more as the PHP reflection API only provides access to the docblock closest to a given structural element.</p><div class="language-php"><pre><code><span class="token keyword">class</span> <span class="token class-name-definition class-name">Controller</span>
<span class="token punctuation">{</span>
    <span class="token comment">/**
     * @OA\\Delete(
     *      path=&quot;/api/v0.0.2/notifications/{id}&quot;,
     *      operationId=&quot;deleteNotificationById&quot;,
     *      summary=&quot;Delete notification by ID&quot;,
     *      @OA\\Parameter(name=&quot;id&quot;, in=&quot;path&quot;, @OA\\Schema(type=&quot;integer&quot;)),
     *      @OA\\Response(response=200, description=&quot;OK&quot;),
     *      @OA\\Response(response=400, description=&quot;Bad Request&quot;)
     * )
     */</span>
    <span class="token comment">/**
     * Delete notification by ID.
     *
     * @param Request $request
     * @param AppNotification $notification
     *
     * @return Response
     * @throws Exception
     */</span>
    <span class="token keyword">public</span> <span class="token keyword">function</span> <span class="token function-definition function">destroy</span><span class="token punctuation">(</span><span class="token class-name type-declaration">Request</span> <span class="token variable">$request</span><span class="token punctuation">,</span> <span class="token class-name type-declaration">AppNotification</span> <span class="token variable">$notification</span><span class="token punctuation">)</span> <span class="token punctuation">{</span>
        <span class="token comment">//</span>
    <span class="token punctuation">}</span>
<span class="token punctuation">}</span>
</code></pre></div><p>In this case the simplest solution is to merge both docblocks. As an additional benefit the duplication of the summary can be avoided.</p><p>In this improved version <code>swagger-php</code> will automatically use the docblock summary just as explicitly done above.</p><div class="language-php"><pre><code><span class="token keyword">class</span> <span class="token class-name-definition class-name">Controller</span>
<span class="token punctuation">{</span>
    <span class="token comment">/**
     * Delete notification by ID.
     *
     * @OA\\Delete(
     *      path=&quot;/api/v0.0.2/notifications/{id}&quot;,
     *      operationId=&quot;deleteNotificationById&quot;,
     *      @OA\\Parameter(name=&quot;id&quot;, in=&quot;path&quot;, @OA\\Schema(type=&quot;integer&quot;)),
     *      @OA\\Response(response=200, description=&quot;OK&quot;),
     *      @OA\\Response(response=400, description=&quot;Bad Request&quot;)
     * )
     *
     * @param Request $request
     * @param AppNotification $notification
     *
     * @return Response
     * @throws Exception
     */</span>
    <span class="token keyword">public</span> <span class="token keyword">function</span> <span class="token function-definition function">destroy</span><span class="token punctuation">(</span><span class="token class-name type-declaration">Request</span> <span class="token variable">$request</span><span class="token punctuation">,</span> <span class="token class-name type-declaration">AppNotification</span> <span class="token variable">$notification</span><span class="token punctuation">)</span> <span class="token punctuation">{</span>
        <span class="token comment">//</span>
    <span class="token punctuation">}</span>
<span class="token punctuation">}</span>
</code></pre></div><p><strong>Resulting spec:</strong></p><div class="language-yaml"><pre><code><span class="token key atrule">openapi</span><span class="token punctuation">:</span> 3.0.0
<span class="token key atrule">paths</span><span class="token punctuation">:</span>
  <span class="token key atrule">&#39;/api/v0.0.2/notifications/{id}&#39;</span><span class="token punctuation">:</span>
    <span class="token key atrule">delete</span><span class="token punctuation">:</span>
      <span class="token key atrule">summary</span><span class="token punctuation">:</span> <span class="token string">&#39;XDelete notification by ID.&#39;</span>
      <span class="token key atrule">operationId</span><span class="token punctuation">:</span> deleteNotificationById
      <span class="token key atrule">parameters</span><span class="token punctuation">:</span>
        <span class="token punctuation">-</span>
          <span class="token key atrule">name</span><span class="token punctuation">:</span> id
          <span class="token key atrule">in</span><span class="token punctuation">:</span> path
          <span class="token key atrule">schema</span><span class="token punctuation">:</span>
            <span class="token key atrule">type</span><span class="token punctuation">:</span> integer
      <span class="token key atrule">responses</span><span class="token punctuation">:</span>
        <span class="token key atrule">&#39;200&#39;</span><span class="token punctuation">:</span>
          <span class="token key atrule">description</span><span class="token punctuation">:</span> OK
        <span class="token key atrule">&#39;400&#39;</span><span class="token punctuation">:</span>
          <span class="token key atrule">description</span><span class="token punctuation">:</span> <span class="token string">&#39;Bad Request&#39;</span>

</code></pre></div><h2 id="skipping-unknown-someclass" tabindex="-1">Skipping unknown <code>\\SomeClass</code> <a class="header-anchor" href="#skipping-unknown-someclass" aria-hidden="true">#</a></h2><p>This message means that <code>swagger-php</code> has tried to use reflection to inspect <code>\\SomeClass</code> and that PHP could not find/load that class. Effectively, this means that <code>class_exists(&quot;\\SomeClass&quot;)</code> returns <code>false</code>.</p><h3 id="using-the-b-bootstrap-option" tabindex="-1">Using the <code>-b</code> <code>--bootstrap</code> option <a class="header-anchor" href="#using-the-b-bootstrap-option" aria-hidden="true">#</a></h3><p>There are a number of reasons why this could happen. If you are using the <code>openapi</code> command line tool from a global installation typically the application classloader (composer) is not active. With you application root being <code>myapp</code> you could try:</p><div class="language-shell"><pre><code>openapi <span class="token parameter variable">-b</span> myapp/vendor/autoload.php myapp/src
</code></pre></div><p>The <code>-b</code> allows to execute some extra PHP code to load whatever is needed to register your apps classloader with PHP.</p><h3 id="namespace-mismatch" tabindex="-1">Namespace mismatch <a class="header-anchor" href="#namespace-mismatch" aria-hidden="true">#</a></h3><p>Another reason for this error could be that your class actually has the wrong namespace (or no namespace at all!).</p><p>Depending on your framework this might still work in the context of your app, but the composer autoloader alone might not be able to load your class (assuming you are using composer).</p><h2 id="no-output-from-openapi-command-line-tool" tabindex="-1">No output from <code>openapi</code> command line tool <a class="header-anchor" href="#no-output-from-openapi-command-line-tool" aria-hidden="true">#</a></h2><p>Depending on your PHP configuration, running the <code>openapi</code> command line tool might result in no output at all.</p><p>The reason for this is that <code>openapi</code> currently uses the <a href="https://www.php.net/manual/en/function.error-log.php" target="_blank" rel="noopener noreferrer"><code>error_log</code></a> function for all output.</p><p>So if this is configured to write to a file, then it will seem like the command is broken.</p>`,34),i=[o];function p(c,l,r,u,d,h){return a(),e("div",null,i)}var f=n(t,[["render",p]]);export{k as __pageData,f as default};
