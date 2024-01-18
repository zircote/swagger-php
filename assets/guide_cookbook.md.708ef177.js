import{_ as o,c as p,b as c,w as a,a as e,r as u,o as l,d as n,e as s}from"./app.cfc21d1f.js";const O='{"title":"Cookbook","description":"","frontmatter":{},"headers":[{"level":2,"title":"x-tagGroups","slug":"x-taggroups"},{"level":2,"title":"Adding examples to @OA\\\\Response","slug":"adding-examples-to-oa-response"},{"level":2,"title":"External documentation","slug":"external-documentation"},{"level":2,"title":"Properties with union types","slug":"properties-with-union-types"},{"level":2,"title":"Referencing a security scheme","slug":"referencing-a-security-scheme"},{"level":2,"title":"File upload with headers","slug":"file-upload-with-headers"},{"level":2,"title":"Set the XML root name","slug":"set-the-xml-root-name"},{"level":2,"title":"upload multipart/form-data","slug":"upload-multipart-form-data"},{"level":2,"title":"Default security scheme for all endpoints","slug":"default-security-scheme-for-all-endpoints"},{"level":2,"title":"Nested objects","slug":"nested-objects"},{"level":2,"title":"Documenting union type response data using oneOf","slug":"documenting-union-type-response-data-using-oneof"},{"level":2,"title":"Reusing responses","slug":"reusing-responses"},{"level":2,"title":"mediaType=\\"/\\"","slug":"mediatype"},{"level":2,"title":"Warning about Multiple response with same response=\\"200\\"","slug":"warning-about-multiple-response-with-same-response-200"},{"level":2,"title":"Callbacks","slug":"callbacks"},{"level":2,"title":"(Mostly) virtual models","slug":"mostly-virtual-models"},{"level":2,"title":"Using class name as type instead of references","slug":"using-class-name-as-type-instead-of-references"},{"level":2,"title":"Enums","slug":"enums"},{"level":2,"title":"Multi value query parameter: &q[]=1&q[]=1","slug":"multi-value-query-parameter-q-1-q-1"},{"level":2,"title":"Custom response classes","slug":"custom-response-classes"},{"level":2,"title":"Annotating class constants","slug":"annotating-class-constants"}],"relativePath":"guide/cookbook.md"}',i={},r=e(`<h1 id="cookbook" tabindex="-1">Cookbook <a class="header-anchor" href="#cookbook" aria-hidden="true">#</a></h1><h2 id="x-taggroups" tabindex="-1"><code>x-tagGroups</code> <a class="header-anchor" href="#x-taggroups" aria-hidden="true">#</a></h2><p>OpenApi has the concept of grouping endpoints using tags. On top of that, some tools (<a href="https://redoc.ly/docs/api-reference-docs/specification-extensions/x-tag-groups/" target="_blank" rel="noopener noreferrer">redocly</a>, for example) support further grouping via the vendor extension <code>x-tagGroups</code>.</p><div class="language-php"><pre><code><span class="token comment">/**
 * @OA\\OpenApi(
 *   x={
 *       &quot;tagGroups&quot;=
 *           {{&quot;name&quot;=&quot;User Management&quot;, &quot;tags&quot;={&quot;Users&quot;, &quot;API keys&quot;, &quot;Admin&quot;}}
 *       }
 *   }
 * )
 */</span>
</code></pre></div><h2 id="adding-examples-to-oa-response" tabindex="-1">Adding examples to <code>@OA\\Response</code> <a class="header-anchor" href="#adding-examples-to-oa-response" aria-hidden="true">#</a></h2><div class="language-php"><pre><code><span class="token comment">/*
 * @OA\\Response(
 *     response=200,
 *     description=&quot;OK&quot;,
 *     @OA\\JsonContent(
 *         oneOf={
 *             @OA\\Schema(ref=&quot;#/components/schemas/Result&quot;),
 *             @OA\\Schema(type=&quot;boolean&quot;)
 *         },
 *         @OA\\Examples(example=&quot;result&quot;, value={&quot;success&quot;: true}, summary=&quot;An result object.&quot;),
 *         @OA\\Examples(example=&quot;bool&quot;, value=false, summary=&quot;A boolean value.&quot;),
 *     )
 * )
 */</span>
</code></pre></div><h2 id="external-documentation" tabindex="-1">External documentation <a class="header-anchor" href="#external-documentation" aria-hidden="true">#</a></h2><p>OpenApi allows a single reference to external documentation. This is a part of the top level <code>@OA\\OpenApi</code>.</p><div class="language-php"><pre><code><span class="token comment">/**
 * @OA\\OpenApi(
 *   @OA\\ExternalDocumentation(
 *     description=&quot;More documentation here...&quot;,
 *     url=&quot;https://example.com/externaldoc1/&quot;
 *   )
 * )
 */</span>
</code></pre></div><div class="tip custom-block"><p class="custom-block-title">TIP</p><p>If no <code>@OA\\OpenApi</code> is configured, <code>swagger-php</code> will create one automatically.</p><p>That means the above example would also work with just the <code>OA\\ExternalDocumentation</code> annotation.</p><div class="language-php"><pre><code><span class="token comment">/**
 * @OA\\ExternalDocumentation(
 *   description=&quot;More documentation here...&quot;,
 *   url=&quot;https://example.com/externaldoc1/&quot;
 * )
 */</span>
</code></pre></div></div><h2 id="properties-with-union-types" tabindex="-1">Properties with union types <a class="header-anchor" href="#properties-with-union-types" aria-hidden="true">#</a></h2><p>Sometimes properties or even lists (arrays) may contain data of different types. This can be expressed using <code>oneOf</code>.</p><div class="language-php"><pre><code><span class="token comment">/**
 * @OA\\Schema(
 *      schema=&quot;StringList&quot;,
 *      @OA\\Property(property=&quot;value&quot;, type=&quot;array&quot;, @OA\\Items(anyOf={@OA\\Schema(type=&quot;string&quot;)}))
 * )
 * @OA\\Schema(
 *      schema=&quot;String&quot;,
 *      @OA\\Property(property=&quot;value&quot;, type=&quot;string&quot;)
 * )
 * @OA\\Schema(
 *      schema=&quot;Object&quot;,
 *      @OA\\Property(property=&quot;value&quot;, type=&quot;object&quot;)
 * )
 * @OA\\Schema(
 *     schema=&quot;mixedList&quot;,
 *     @OA\\Property(property=&quot;fields&quot;, type=&quot;array&quot;, @OA\\Items(oneOf={
 *         @OA\\Schema(ref=&quot;#/components/schemas/StringList&quot;),
 *         @OA\\Schema(ref=&quot;#/components/schemas/String&quot;),
 *         @OA\\Schema(ref=&quot;#/components/schemas/Object&quot;)
 *     }))
 * )
 */</span>
</code></pre></div><p>This will resolve into this YAML</p><div class="language-yaml"><pre><code><span class="token key atrule">openapi</span><span class="token punctuation">:</span> 3.0.0
<span class="token key atrule">components</span><span class="token punctuation">:</span>
  <span class="token key atrule">schemas</span><span class="token punctuation">:</span>
    <span class="token key atrule">StringList</span><span class="token punctuation">:</span>
      <span class="token key atrule">properties</span><span class="token punctuation">:</span>
        <span class="token key atrule">value</span><span class="token punctuation">:</span>
          <span class="token key atrule">type</span><span class="token punctuation">:</span> array
          <span class="token key atrule">items</span><span class="token punctuation">:</span>
            <span class="token key atrule">anyOf</span><span class="token punctuation">:</span>
              <span class="token punctuation">-</span>
                <span class="token key atrule">type</span><span class="token punctuation">:</span> string
      <span class="token key atrule">type</span><span class="token punctuation">:</span> object
    <span class="token key atrule">String</span><span class="token punctuation">:</span>
      <span class="token key atrule">properties</span><span class="token punctuation">:</span>
        <span class="token key atrule">value</span><span class="token punctuation">:</span>
          <span class="token key atrule">type</span><span class="token punctuation">:</span> string
      <span class="token key atrule">type</span><span class="token punctuation">:</span> object
    <span class="token key atrule">Object</span><span class="token punctuation">:</span>
      <span class="token key atrule">properties</span><span class="token punctuation">:</span>
        <span class="token key atrule">value</span><span class="token punctuation">:</span>
          <span class="token key atrule">type</span><span class="token punctuation">:</span> object
      <span class="token key atrule">type</span><span class="token punctuation">:</span> object
    <span class="token key atrule">mixedList</span><span class="token punctuation">:</span>
      <span class="token key atrule">properties</span><span class="token punctuation">:</span>
        <span class="token key atrule">fields</span><span class="token punctuation">:</span>
          <span class="token key atrule">type</span><span class="token punctuation">:</span> array
          <span class="token key atrule">items</span><span class="token punctuation">:</span>
            <span class="token key atrule">oneOf</span><span class="token punctuation">:</span>
              <span class="token punctuation">-</span>
                <span class="token key atrule">$ref</span><span class="token punctuation">:</span> <span class="token string">&#39;#/components/schemas/StringList&#39;</span>
              <span class="token punctuation">-</span>
                <span class="token key atrule">$ref</span><span class="token punctuation">:</span> <span class="token string">&#39;#/components/schemas/String&#39;</span>
              <span class="token punctuation">-</span>
                <span class="token key atrule">$ref</span><span class="token punctuation">:</span> <span class="token string">&#39;#/components/schemas/Object&#39;</span>
      <span class="token key atrule">type</span><span class="token punctuation">:</span> object
</code></pre></div><h2 id="referencing-a-security-scheme" tabindex="-1">Referencing a security scheme <a class="header-anchor" href="#referencing-a-security-scheme" aria-hidden="true">#</a></h2><p>An API might have zero or more security schemes. These are defined at the top level and vary from simple to complex:</p><div class="language-php"><pre><code><span class="token comment">/**
 * @OA\\SecurityScheme(
 *     type=&quot;apiKey&quot;,
 *     name=&quot;api_key&quot;,
 *     in=&quot;header&quot;,
 *     securityScheme=&quot;api_key&quot;
 * )
 *
 * @OA\\SecurityScheme(
 *   type=&quot;oauth2&quot;,
 *   securityScheme=&quot;petstore_auth&quot;,
 *   @OA\\Flow(
 *      authorizationUrl=&quot;http://petstore.swagger.io/oauth/dialog&quot;,
 *      flow=&quot;implicit&quot;,
 *      scopes={
 *         &quot;read:pets&quot;: &quot;read your pets&quot;,
 *         &quot;write:pets&quot;: &quot;modify pets in your account&quot;
 *      }
 *   )
 * )
 */</span>
</code></pre></div><p>To declare an endpoint as secure and define what security schemes are available to authenticate a client it needs to be added to the operation, for example:</p><div class="language-php"><pre><code><span class="token comment">/**
 * @OA\\Get(
 *      path=&quot;/api/secure/&quot;,
 *      summary=&quot;Requires authentication&quot;
 *    ),
 *    security={ {&quot;api_key&quot;: {}} }
 * )
 */</span>
</code></pre></div><div class="tip custom-block"><p class="custom-block-title">Endpoints can support multiple security schemes and have custom options too:</p><div class="language-php"><pre><code><span class="token comment">/**
 * @OA\\Get(
 *      path=&quot;/api/secure/&quot;,
 *      summary=&quot;Requires authentication&quot;
 *    ),
 *    security={
 *      { &quot;api_key&quot;: {} },
 *      { &quot;petstore_auth&quot;: {&quot;write:pets&quot;, &quot;read:pets&quot;} }
 *    }
 * )
 */</span>
</code></pre></div></div><h2 id="file-upload-with-headers" tabindex="-1">File upload with headers <a class="header-anchor" href="#file-upload-with-headers" aria-hidden="true">#</a></h2><div class="language-php"><pre><code><span class="token comment">/**
 * @OA\\Post(
 *   path=&quot;/v1/media/upload&quot;,
 *   summary=&quot;Upload document&quot;,
 *   description=&quot;&quot;,
 *   tags={&quot;Media&quot;},
 *   @OA\\RequestBody(
 *     required=true,
 *     @OA\\MediaType(
 *       mediaType=&quot;application/octet-stream&quot;,
 *       @OA\\Schema(
 *         required={&quot;content&quot;},
 *         @OA\\Property(
 *           description=&quot;Binary content of file&quot;,
 *           property=&quot;content&quot;,
 *           type=&quot;string&quot;,
 *           format=&quot;binary&quot;
 *         )
 *       )
 *     )
 *   ),
 *   @OA\\Response(
 *     response=200, description=&quot;Success&quot;,
 *     @OA\\Schema(type=&quot;string&quot;)
 *   ),
 *   @OA\\Response(
 *     response=400, description=&quot;Bad Request&quot;
 *   )
 * )
 */</span>
</code></pre></div><h2 id="set-the-xml-root-name" tabindex="-1">Set the XML root name <a class="header-anchor" href="#set-the-xml-root-name" aria-hidden="true">#</a></h2><p>The <code>OA\\Xml</code> annotation may be used to set the XML root element for a given <code>@OA\\XmlContent</code> response body</p><div class="language-php"><pre><code><span class="token comment">/**
 * @OA\\Schema(
 *     schema=&quot;Error&quot;,
 *     @OA\\Property(property=&quot;message&quot;),
 *     @OA\\Xml(name=&quot;details&quot;)
 * )
 */</span>

<span class="token comment">/**
 * @OA\\Post(
 *     path=&quot;/foobar&quot;,
 *     @OA\\Response(
 *         response=400,
 *         description=&quot;Request error&quot;,
 *         @OA\\XmlContent(ref=&quot;#/components/schemas/Error&quot;,
 *           @OA\\Xml(name=&quot;error&quot;)
 *        )
 *     )
 * )
 */</span>
</code></pre></div><h2 id="upload-multipart-form-data" tabindex="-1">upload multipart/form-data <a class="header-anchor" href="#upload-multipart-form-data" aria-hidden="true">#</a></h2><p>Form posts are <code>@OA\\Post</code> requests with a <code>multipart/form-data</code> <code>@OA\\RequestBody</code>. The relevant bit looks something like this</p><div class="language-php"><pre><code><span class="token comment">/**
 * @OA\\Post(
 *   path=&quot;/v1/user/update&quot;,
 *   summary=&quot;Form post&quot;,
 *   @OA\\RequestBody(
 *     @OA\\MediaType(
 *       mediaType=&quot;multipart/form-data&quot;,
 *       @OA\\Schema(
 *         @OA\\Property(property=&quot;name&quot;),
 *         @OA\\Property(
 *           description=&quot;file to upload&quot;,
 *           property=&quot;avatar&quot;,
 *           type=&quot;string&quot;,
 *           format=&quot;binary&quot;,
 *         ),
 *       )
 *     )
 *   ),
 *   @OA\\Response(response=200, description=&quot;Success&quot;)
 * )
 */</span>
</code></pre></div><h2 id="default-security-scheme-for-all-endpoints" tabindex="-1">Default security scheme for all endpoints <a class="header-anchor" href="#default-security-scheme-for-all-endpoints" aria-hidden="true">#</a></h2><p>Unless specified each endpoint needs to declare what security schemes it supports. However, there is a way to also configure security schemes globally for the whole API.</p><p>This is done on the <code>@OA\\OpenApi</code> annotations:</p>`,32),d=n("div",{class:"language-php"},[n("pre",null,[n("code",null,[n("span",{class:"token php language-php"},[n("span",{class:"token delimiter important"},"<?php"),s(`

`),n("span",{class:"token keyword"},"use"),s(),n("span",{class:"token package"},[s("OpenApi"),n("span",{class:"token punctuation"},"\\"),s("Annotations")]),s(),n("span",{class:"token keyword"},"as"),s(),n("span",{class:"token constant"},"OA"),n("span",{class:"token punctuation"},";"),s(`

`),n("span",{class:"token comment"},`/**
 * @OA\\OpenApi(
 *   security={{"bearerAuth": {}}}
 * )
 *
 * @OA\\SecurityScheme(
 *   securityScheme="bearerAuth",
 *   type="http",
 *   scheme="bearer"
 * )
 */`),s(`
`),n("span",{class:"token keyword"},"class"),s(),n("span",{class:"token class-name-definition class-name"},"OpenApi"),s(`
`),n("span",{class:"token punctuation"},"{"),s(`
`),n("span",{class:"token punctuation"},"}"),s(`
`)])])])],-1),k=n("div",{class:"language-php"},[n("pre",null,[n("code",null,[n("span",{class:"token php language-php"},[n("span",{class:"token delimiter important"},"<?php"),s(`

`),n("span",{class:"token keyword"},"use"),s(),n("span",{class:"token package"},[s("OpenApi"),n("span",{class:"token punctuation"},"\\"),s("Attributes")]),s(),n("span",{class:"token keyword"},"as"),s(),n("span",{class:"token constant"},"OAT"),n("span",{class:"token punctuation"},";"),s(`

`),n("span",{class:"token attribute"},[n("span",{class:"token delimiter punctuation"},"#["),n("span",{class:"token attribute-content"},[n("span",{class:"token attribute-class-name class-name class-name-fully-qualified"},[s("OAT"),n("span",{class:"token punctuation"},"\\"),s("OpenApi")]),n("span",{class:"token punctuation"},"("),s(`
    `),n("span",{class:"token attribute-class-name class-name"},"security"),n("span",{class:"token punctuation"},":"),s(),n("span",{class:"token punctuation"},"["),n("span",{class:"token punctuation"},"["),n("span",{class:"token string single-quoted-string"},"'bearerAuth'"),s(),n("span",{class:"token operator"},"=>"),s(),n("span",{class:"token punctuation"},"["),n("span",{class:"token punctuation"},"]"),n("span",{class:"token punctuation"},"]"),n("span",{class:"token punctuation"},"]"),s(`
`),n("span",{class:"token punctuation"},")")]),n("span",{class:"token delimiter punctuation"},"]")]),s(`
`),n("span",{class:"token attribute"},[n("span",{class:"token delimiter punctuation"},"#["),n("span",{class:"token attribute-content"},[n("span",{class:"token attribute-class-name class-name class-name-fully-qualified"},[s("OAT"),n("span",{class:"token punctuation"},"\\"),s("Components")]),n("span",{class:"token punctuation"},"("),s(`
    `),n("span",{class:"token attribute-class-name class-name"},"securitySchemes"),n("span",{class:"token punctuation"},":"),s(),n("span",{class:"token punctuation"},"["),s(`
        `),n("span",{class:"token attribute-class-name class-name"},"new"),s(),n("span",{class:"token attribute-class-name class-name class-name-fully-qualified"},[s("OAT"),n("span",{class:"token punctuation"},"\\"),s("SecurityScheme")]),n("span",{class:"token punctuation"},"("),s(`
            `),n("span",{class:"token attribute-class-name class-name"},"securityScheme"),n("span",{class:"token punctuation"},":"),s(),n("span",{class:"token string single-quoted-string"},"'bearerAuth'"),n("span",{class:"token punctuation"},","),s(`
            `),n("span",{class:"token attribute-class-name class-name"},"type"),n("span",{class:"token punctuation"},":"),s(),n("span",{class:"token string single-quoted-string"},"'http'"),n("span",{class:"token punctuation"},","),s(`
            `),n("span",{class:"token attribute-class-name class-name"},"scheme"),n("span",{class:"token punctuation"},":"),s(),n("span",{class:"token string single-quoted-string"},"'bearer'"),s(`
        `),n("span",{class:"token punctuation"},")"),s(`
    `),n("span",{class:"token punctuation"},"]"),s(`
`),n("span",{class:"token punctuation"},")")]),n("span",{class:"token delimiter punctuation"},"]")]),s(`
`),n("span",{class:"token keyword"},"class"),s(),n("span",{class:"token class-name-definition class-name"},"OpenApiSpec"),s(`
`),n("span",{class:"token punctuation"},"{"),s(`
`),n("span",{class:"token punctuation"},"}"),s(`
`)])])])],-1),m=e(`<h2 id="nested-objects" tabindex="-1">Nested objects <a class="header-anchor" href="#nested-objects" aria-hidden="true">#</a></h2><p>Complex, nested data structures are defined by nesting <code>@OA\\Property</code> annotations inside others (with <code>type=&quot;object&quot;</code>).</p><div class="language-php"><pre><code><span class="token comment">/**
 *  @OA\\Schema(
 *    schema=&quot;Profile&quot;,
 *    type=&quot;object&quot;,
*
 *    @OA\\Property(
 *      property=&quot;Status&quot;,
 *      type=&quot;string&quot;,
 *      example=&quot;0&quot;
 *    ),
 *
 *    @OA\\Property(
 *      property=&quot;Group&quot;,
 *      type=&quot;object&quot;,
 *
 *      @OA\\Property(
 *        property=&quot;ID&quot;,
 *        description=&quot;ID de grupo&quot;,
 *        type=&quot;number&quot;,
 *        example=-1
 *      ),
 *
 *      @OA\\Property(
 *        property=&quot;Name&quot;,
 *        description=&quot;Nombre de grupo&quot;,
 *        type=&quot;string&quot;,
 *        example=&quot;Superadmin&quot;
 *      )
 *    )
 *  )
 */</span>

</code></pre></div><h2 id="documenting-union-type-response-data-using-oneof" tabindex="-1">Documenting union type response data using <code>oneOf</code> <a class="header-anchor" href="#documenting-union-type-response-data-using-oneof" aria-hidden="true">#</a></h2><p>A response with either a single or a list of <code>QualificationHolder</code>&#39;s.</p><div class="language-php"><pre><code><span class="token comment">/**
 * @OA\\Response(
 *     response=200,
 *     @OA\\JsonContent(
 *         oneOf={
 *             @OA\\Schema(ref=&quot;#/components/schemas/QualificationHolder&quot;),
 *             @OA\\Schema(
 *                 type=&quot;array&quot;,
 *                 @OA\\Items(ref=&quot;#/components/schemas/QualificationHolder&quot;)
 *             )
 *         }
 *     )
 * )
 */</span>
</code></pre></div><h2 id="reusing-responses" tabindex="-1">Reusing responses <a class="header-anchor" href="#reusing-responses" aria-hidden="true">#</a></h2><p>Global responses are found under <code>/components/responses</code> and can be referenced/shared just like schema definitions (models)</p><div class="language-php"><pre><code><span class="token comment">/**
 * @OA\\Response(
 *   response=&quot;product&quot;,
 *   description=&quot;All information about a product&quot;,
 *   @OA\\JsonContent(ref=&quot;#/components/schemas/Product&quot;)
 * )
 */</span>
<span class="token keyword">class</span> <span class="token class-name-definition class-name">ProductResponse</span> <span class="token punctuation">{</span><span class="token punctuation">}</span>

 <span class="token comment">// ...</span>

<span class="token keyword">class</span> <span class="token class-name-definition class-name">ProductController</span>
<span class="token punctuation">{</span>
    <span class="token comment">/**
     * @OA\\Get(
     *   tags={&quot;Products&quot;},
     *   path=&quot;/products/{product_id}&quot;,
     *   @OA\\Response(
     *       response=&quot;default&quot;,
     *       ref=&quot;#/components/responses/product&quot;
     *   )
     * )
     */</span>
    <span class="token keyword">public</span> <span class="token keyword">function</span> <span class="token function-definition function">getProduct</span><span class="token punctuation">(</span><span class="token variable">$id</span><span class="token punctuation">)</span>
    <span class="token punctuation">{</span>
    <span class="token punctuation">}</span>
<span class="token punctuation">}</span>
</code></pre></div><div class="tip custom-block"><p class="custom-block-title">\`response\` parameter is always required</p><p>Even if referencing a shared response definition, the <code>response</code> parameter is still required.</p></div><h2 id="mediatype" tabindex="-1">mediaType=&quot;<em>/</em>&quot; <a class="header-anchor" href="#mediatype" aria-hidden="true">#</a></h2><p>Using <code>*/*</code> as <code>mediaType</code> is not possible using annotations.</p><p><strong>Example:</strong></p><div class="language-php"><pre><code><span class="token comment">/**
 * @OA\\MediaType(
 *     mediaType=&quot;*/</span><span class="token operator">*</span><span class="token string double-quoted-string">&quot;,
 *     @OA\\Schema(type=&quot;</span><span class="token keyword type-declaration">string</span><span class="token string double-quoted-string">&quot;,format=&quot;</span>binary&quot;<span class="token punctuation">)</span>
 <span class="token operator">*</span> <span class="token punctuation">)</span>
 <span class="token operator">*</span><span class="token operator">/</span>
</code></pre></div><p>The doctrine annotations library used for parsing annotations does not handle this and will interpret the <code>*/</code> bit as the end of the comment.</p><p>Using just <code>*</code> or <code>application/octet-stream</code> might be usable workarounds.</p><h2 id="warning-about-multiple-response-with-same-response-200" tabindex="-1">Warning about <code>Multiple response with same response=&quot;200&quot;</code> <a class="header-anchor" href="#warning-about-multiple-response-with-same-response-200" aria-hidden="true">#</a></h2><p>There are two scenarios where this can happen</p><ol><li>A single endpoint contains two responses with the same <code>response</code> value.</li><li>There are multiple global response declared, again more than one with the same <code>response</code> value.</li></ol><h2 id="callbacks" tabindex="-1">Callbacks <a class="header-anchor" href="#callbacks" aria-hidden="true">#</a></h2><p>The API does include basic support for callbacks. However, this needs to be set up mostly manually.</p><p><strong>Example</strong></p><div class="language-php"><pre><code><span class="token comment">/**
 *     ...
 *
 *     callbacks={
 *         &quot;onChange&quot;={
 *              &quot;{$request.query.callbackUrl}&quot;={
 *                  &quot;post&quot;: {
 *                      &quot;requestBody&quot;: @OA\\RequestBody(
 *                          description=&quot;subscription payload&quot;,
 *                          @OA\\MediaType(mediaType=&quot;application/json&quot;, @OA\\Schema(
 *                              @OA\\Property(property=&quot;timestamp&quot;, type=&quot;string&quot;, format=&quot;date-time&quot;, description=&quot;time of change&quot;)
 *                          ))
 *                      )
 *                  },
 *                  &quot;responses&quot;: {
 *                      &quot;202&quot;: {
 *                          &quot;description&quot;: &quot;Your server implementation should return this HTTP status code if the data was received successfully&quot;
 *                      }
 *                  }
 *              }
 *         }
 *     }
 *
 *     ...
 *
 */</span>
</code></pre></div><h2 id="mostly-virtual-models" tabindex="-1">(Mostly) virtual models <a class="header-anchor" href="#mostly-virtual-models" aria-hidden="true">#</a></h2><p>Typically, a model is annotated by adding a <code>@OA\\Schema</code> annotation to the class and then individual <code>@OA\\Property</code> annotations to the individually declared class properties.</p><p>It is possible, however, to nest <code>O@\\Property</code> annotations inside a schema even without properties. In fact, all that is needed is a code anchor - e.g. an empty class.</p><div class="language-php"><pre><code><span class="token keyword">use</span> <span class="token package">OpenApi<span class="token punctuation">\\</span>Attributes</span> <span class="token keyword">as</span> <span class="token constant">OA</span><span class="token punctuation">;</span>

<span class="token attribute"><span class="token delimiter punctuation">#[</span><span class="token attribute-content"><span class="token attribute-class-name class-name class-name-fully-qualified">OA<span class="token punctuation">\\</span>Schema</span><span class="token punctuation">(</span>
    <span class="token attribute-class-name class-name">properties</span><span class="token punctuation">:</span> <span class="token punctuation">[</span>
        <span class="token string single-quoted-string">&#39;name&#39;</span> <span class="token operator">=&gt;</span> <span class="token attribute-class-name class-name">new</span> <span class="token attribute-class-name class-name class-name-fully-qualified">OA<span class="token punctuation">\\</span>Property</span><span class="token punctuation">(</span><span class="token attribute-class-name class-name">property</span><span class="token punctuation">:</span> <span class="token string single-quoted-string">&#39;name&#39;</span><span class="token punctuation">,</span> <span class="token attribute-class-name class-name">type</span><span class="token punctuation">:</span> <span class="token string single-quoted-string">&#39;string&#39;</span><span class="token punctuation">)</span><span class="token punctuation">,</span>
        <span class="token string single-quoted-string">&#39;email&#39;</span> <span class="token operator">=&gt;</span> <span class="token attribute-class-name class-name">new</span> <span class="token attribute-class-name class-name class-name-fully-qualified">OA<span class="token punctuation">\\</span>Property</span><span class="token punctuation">(</span><span class="token attribute-class-name class-name">property</span><span class="token punctuation">:</span> <span class="token string single-quoted-string">&#39;email&#39;</span><span class="token punctuation">,</span> <span class="token attribute-class-name class-name">type</span><span class="token punctuation">:</span> <span class="token string single-quoted-string">&#39;string&#39;</span><span class="token punctuation">)</span><span class="token punctuation">,</span>
    <span class="token punctuation">]</span>
<span class="token punctuation">)</span></span><span class="token delimiter punctuation">]</span></span>
<span class="token keyword">class</span> <span class="token class-name-definition class-name">User</span> <span class="token punctuation">{</span><span class="token punctuation">}</span>
</code></pre></div><h2 id="using-class-name-as-type-instead-of-references" tabindex="-1">Using class name as type instead of references <a class="header-anchor" href="#using-class-name-as-type-instead-of-references" aria-hidden="true">#</a></h2><p>Typically, when referencing schemas this is done using <code>$ref</code>&#39;s</p><div class="language-php"><pre><code><span class="token attribute"><span class="token delimiter punctuation">#[</span><span class="token attribute-content"><span class="token attribute-class-name class-name class-name-fully-qualified">OAT<span class="token punctuation">\\</span>Schema</span><span class="token punctuation">(</span><span class="token attribute-class-name class-name">schema</span><span class="token punctuation">:</span> <span class="token string single-quoted-string">&#39;user&#39;</span><span class="token punctuation">)</span></span><span class="token delimiter punctuation">]</span></span>
<span class="token keyword">class</span> <span class="token class-name-definition class-name">User</span>
<span class="token punctuation">{</span>
<span class="token punctuation">}</span>

<span class="token attribute"><span class="token delimiter punctuation">#[</span><span class="token attribute-content"><span class="token attribute-class-name class-name class-name-fully-qualified">OAT<span class="token punctuation">\\</span>Schema</span><span class="token punctuation">(</span><span class="token punctuation">)</span></span><span class="token delimiter punctuation">]</span></span>
<span class="token keyword">class</span> <span class="token class-name-definition class-name">Book</span>
<span class="token punctuation">{</span>
    <span class="token comment">/**
     * @var User
     */</span>
    <span class="token attribute"><span class="token delimiter punctuation">#[</span><span class="token attribute-content"><span class="token attribute-class-name class-name class-name-fully-qualified">OAT<span class="token punctuation">\\</span>Property</span><span class="token punctuation">(</span><span class="token attribute-class-name class-name">ref</span><span class="token punctuation">:</span> <span class="token string single-quoted-string">&#39;#/components/schemas/user&#39;</span><span class="token punctuation">)</span></span><span class="token delimiter punctuation">]</span></span>
    <span class="token keyword">public</span> <span class="token variable">$author</span><span class="token punctuation">;</span>
<span class="token punctuation">}</span>
</code></pre></div><p>This works, but is not very convenient.</p><p>First, when using custom schema names (<code>schema: &#39;user&#39;</code>), this needs to be taken into account everywhere. Secondly, having to write <code>ref: &#39;#/components/schemas/user&#39;</code> is tedious and error-prone.</p><p>Using attributes all this changes as we can take advantage of PHP itself by referring to a schema by its (fully qualified) class name.</p><p>With the same <code>User</code> schema as before, the <code>Book::author</code> property could be written in a few different ways</p><div class="language-php"><pre><code>    <span class="token attribute"><span class="token delimiter punctuation">#[</span><span class="token attribute-content"><span class="token attribute-class-name class-name class-name-fully-qualified">OAT<span class="token punctuation">\\</span>Property</span><span class="token punctuation">(</span><span class="token punctuation">)</span></span><span class="token delimiter punctuation">]</span></span>
    <span class="token keyword">public</span> User author<span class="token punctuation">;</span>
</code></pre></div><p><strong>or</strong></p><div class="language-php"><pre><code>    <span class="token comment">/**
     * @var User
     */</span>
    <span class="token attribute"><span class="token delimiter punctuation">#[</span><span class="token attribute-content"><span class="token attribute-class-name class-name class-name-fully-qualified">OAT<span class="token punctuation">\\</span>Property</span><span class="token punctuation">(</span><span class="token punctuation">)</span></span><span class="token delimiter punctuation">]</span></span>
    <span class="token keyword">public</span> author<span class="token punctuation">;</span>
</code></pre></div><p><strong>or</strong></p><div class="language-php"><pre><code>    <span class="token attribute"><span class="token delimiter punctuation">#[</span><span class="token attribute-content"><span class="token attribute-class-name class-name class-name-fully-qualified">OAT<span class="token punctuation">\\</span>Property</span><span class="token punctuation">(</span><span class="token attribute-class-name class-name">type</span><span class="token punctuation">:</span> <span class="token attribute-class-name class-name">User</span><span class="token operator">::</span><span class="token constant">class</span><span class="token punctuation">)</span></span><span class="token delimiter punctuation">]</span></span>
    <span class="token keyword">public</span> author<span class="token punctuation">;</span>
</code></pre></div><h2 id="enums" tabindex="-1">Enums <a class="header-anchor" href="#enums" aria-hidden="true">#</a></h2><p>As of PHP 8.1 there is native support for <code>enum</code>&#39;s.</p><p><code>swagger-php</code> supports enums in much the same way as class names can be used to reference schemas.</p><p><strong>Example</strong></p><div class="language-php"><pre><code><span class="token attribute"><span class="token delimiter punctuation">#[</span><span class="token attribute-content"><span class="token attribute-class-name class-name">Schema</span><span class="token punctuation">(</span><span class="token punctuation">)</span></span><span class="token delimiter punctuation">]</span></span>
<span class="token keyword">enum</span> <span class="token class-name-definition class-name">State</span>
<span class="token punctuation">{</span>
    <span class="token keyword">case</span> <span class="token constant">OPEN</span><span class="token punctuation">;</span>
    <span class="token keyword">case</span> <span class="token constant">MERGED</span><span class="token punctuation">;</span>
    <span class="token keyword">case</span> <span class="token constant">DECLINED</span><span class="token punctuation">;</span>
<span class="token punctuation">}</span>

<span class="token attribute"><span class="token delimiter punctuation">#[</span><span class="token attribute-content"><span class="token attribute-class-name class-name">Schema</span><span class="token punctuation">(</span><span class="token punctuation">)</span></span><span class="token delimiter punctuation">]</span></span>
<span class="token keyword">class</span> <span class="token class-name-definition class-name">PullRequest</span>
   <span class="token attribute"><span class="token delimiter punctuation">#[</span><span class="token attribute-content"><span class="token attribute-class-name class-name class-name-fully-qualified">OAT<span class="token punctuation">\\</span>Property</span><span class="token punctuation">(</span><span class="token punctuation">)</span></span><span class="token delimiter punctuation">]</span></span>
   <span class="token keyword">public</span> <span class="token class-name type-declaration">State</span> <span class="token variable">$state</span>
<span class="token punctuation">}</span>
</code></pre></div><p>However, in this case the schema generated for <code>State</code> will be an enum:</p><div class="language-yaml"><pre><code><span class="token key atrule">components</span><span class="token punctuation">:</span>
  <span class="token key atrule">schemas</span><span class="token punctuation">:</span>
    <span class="token key atrule">PullRequest</span><span class="token punctuation">:</span>
      <span class="token key atrule">properties</span><span class="token punctuation">:</span>
        <span class="token key atrule">state</span><span class="token punctuation">:</span>
          <span class="token key atrule">$ref</span><span class="token punctuation">:</span> <span class="token string">&#39;#/components/schemas/State&#39;</span>
      <span class="token key atrule">type</span><span class="token punctuation">:</span> object
    <span class="token key atrule">State</span><span class="token punctuation">:</span>
      <span class="token key atrule">type</span><span class="token punctuation">:</span> string
      <span class="token key atrule">enum</span><span class="token punctuation">:</span>
        <span class="token punctuation">-</span> OPEN
        <span class="token punctuation">-</span> MERGED
        <span class="token punctuation">-</span> DECLINED
</code></pre></div><h2 id="multi-value-query-parameter-q-1-q-1" tabindex="-1">Multi value query parameter: <code>&amp;q[]=1&amp;q[]=1</code> <a class="header-anchor" href="#multi-value-query-parameter-q-1-q-1" aria-hidden="true">#</a></h2><p>PHP allows to have query parameters multiple times in the url and will combine the values to an array if the parameter name uses trailing <code>[]</code>. In fact, it is possible to create nested arrays too by using more than one pair of <code>[]</code>.</p><p>In terms of OpenAPI, the parameters can be considered a single parameter with a list of values.</p><div class="language-php"><pre><code><span class="token comment">/**
 * @OA\\Get(
 *     path=&quot;/api/endpoint&quot;,
 *     description=&quot;The endpoint&quot;,
 *     operationId=&quot;endpoint&quot;,
 *     tags={&quot;endpoints&quot;},
 *     @OA\\Parameter(
 *         name=&quot;things[]&quot;,
 *         in=&quot;query&quot;,
 *         description=&quot;A list of things.&quot;,
 *         required=false,
 *         @OA\\Schema(
 *             type=&quot;array&quot;,
 *             @OA\\Items(type=&quot;integer&quot;)
 *         )
 *     ),
 *     @OA\\Response(response=&quot;200&quot;, description=&quot;All good&quot;)
 * )
 */</span>
</code></pre></div><p>The corresponding bit of the spec will look like this:</p><div class="language-yaml"><pre><code>      <span class="token key atrule">parameters</span><span class="token punctuation">:</span>
        <span class="token punctuation">-</span>
          <span class="token key atrule">name</span><span class="token punctuation">:</span> <span class="token string">&#39;things[]&#39;</span>
          <span class="token key atrule">in</span><span class="token punctuation">:</span> query
          <span class="token key atrule">description</span><span class="token punctuation">:</span> <span class="token string">&#39;A list of things.&#39;</span>
          <span class="token key atrule">required</span><span class="token punctuation">:</span> <span class="token boolean important">false</span>
          <span class="token key atrule">schema</span><span class="token punctuation">:</span>
            <span class="token key atrule">type</span><span class="token punctuation">:</span> array
            <span class="token key atrule">items</span><span class="token punctuation">:</span>
              <span class="token key atrule">type</span><span class="token punctuation">:</span> integer
</code></pre></div><p><code>swagger-ui</code> will show a form that allows to add/remove items (<code>integer</code> values in this case) to/from a list and post those values as something like <code>?things[]=1&amp;things[]=2&amp;things[]=0</code></p><h2 id="custom-response-classes" tabindex="-1">Custom response classes <a class="header-anchor" href="#custom-response-classes" aria-hidden="true">#</a></h2><p>Even with using refs there is a bit of overhead in sharing responses. One way around that is to write your own response classes. The beauty is that in your custom <code>__construct()</code> method you can prefill as much as you need.</p><p>Best of all, this works for both annotations and attributes.</p><p>Example:</p><div class="language-php"><pre><code><span class="token keyword">use</span> <span class="token package">OpenApi<span class="token punctuation">\\</span>Attributes</span> <span class="token keyword">as</span> <span class="token constant">OA</span><span class="token punctuation">;</span>

<span class="token comment">/**
 * @Annotation
 */</span>
<span class="token attribute"><span class="token delimiter punctuation">#[</span><span class="token attribute-content">\\<span class="token attribute-class-name class-name">Attribute</span><span class="token punctuation">(</span><span class="token attribute-class-name class-name class-name-fully-qualified"><span class="token punctuation">\\</span>Attribute</span><span class="token operator">::</span><span class="token constant">TARGET_CLASS</span> <span class="token operator">|</span> <span class="token attribute-class-name class-name class-name-fully-qualified"><span class="token punctuation">\\</span>Attribute</span><span class="token operator">::</span><span class="token constant">TARGET_METHOD</span> <span class="token operator">|</span> <span class="token attribute-class-name class-name class-name-fully-qualified"><span class="token punctuation">\\</span>Attribute</span><span class="token operator">::</span><span class="token constant">IS_REPEATABLE</span><span class="token punctuation">)</span></span><span class="token delimiter punctuation">]</span></span>
<span class="token keyword">class</span> <span class="token class-name-definition class-name">BadRequest</span> <span class="token keyword">extends</span> <span class="token class-name class-name-fully-qualified">OA<span class="token punctuation">\\</span>Response</span>
<span class="token punctuation">{</span>
    <span class="token keyword">public</span> <span class="token keyword">function</span> <span class="token function-definition function">__construct</span><span class="token punctuation">(</span><span class="token punctuation">)</span>
    <span class="token punctuation">{</span>
        <span class="token keyword static-context">parent</span><span class="token operator">::</span><span class="token function">__construct</span><span class="token punctuation">(</span><span class="token argument-name">response</span><span class="token punctuation">:</span> <span class="token number">400</span><span class="token punctuation">,</span> <span class="token argument-name">description</span><span class="token punctuation">:</span> <span class="token string single-quoted-string">&#39;Bad request&#39;</span><span class="token punctuation">)</span><span class="token punctuation">;</span>
    <span class="token punctuation">}</span>
<span class="token punctuation">}</span>

<span class="token keyword">class</span> <span class="token class-name-definition class-name">Controller</span>
<span class="token punctuation">{</span>

    <span class="token attribute"><span class="token delimiter punctuation">#[</span><span class="token attribute-content"><span class="token attribute-class-name class-name class-name-fully-qualified">OA<span class="token punctuation">\\</span>Get</span><span class="token punctuation">(</span><span class="token attribute-class-name class-name">path</span><span class="token punctuation">:</span> <span class="token string single-quoted-string">&#39;/foo&#39;</span><span class="token punctuation">,</span> <span class="token attribute-class-name class-name">responses</span><span class="token punctuation">:</span> <span class="token punctuation">[</span><span class="token attribute-class-name class-name">new</span> <span class="token attribute-class-name class-name">BadRequest</span><span class="token punctuation">(</span><span class="token punctuation">)</span><span class="token punctuation">]</span><span class="token punctuation">)</span></span><span class="token delimiter punctuation">]</span></span>
    <span class="token keyword">public</span> <span class="token keyword">function</span> <span class="token function-definition function">get</span><span class="token punctuation">(</span><span class="token punctuation">)</span>
    <span class="token punctuation">{</span>
    <span class="token punctuation">}</span>

    <span class="token attribute"><span class="token delimiter punctuation">#[</span><span class="token attribute-content"><span class="token attribute-class-name class-name class-name-fully-qualified">OA<span class="token punctuation">\\</span>Post</span><span class="token punctuation">(</span><span class="token attribute-class-name class-name">path</span><span class="token punctuation">:</span> <span class="token string single-quoted-string">&#39;/foo&#39;</span><span class="token punctuation">)</span></span><span class="token delimiter punctuation">]</span></span>
    <span class="token attribute"><span class="token delimiter punctuation">#[</span><span class="token attribute-content"><span class="token attribute-class-name class-name">BadRequest</span></span><span class="token delimiter punctuation">]</span></span>
    <span class="token keyword">public</span> <span class="token keyword">function</span> <span class="token function-definition function">post</span><span class="token punctuation">(</span><span class="token punctuation">)</span>
    <span class="token punctuation">{</span>
    <span class="token punctuation">}</span>

    <span class="token comment">/**
     * @OA\\Delete(
     *     path=&quot;/foo&quot;,
     *     @BadRequest()
     * )
     */</span>
    <span class="token keyword">public</span> <span class="token keyword">function</span> <span class="token function-definition function">delete</span><span class="token punctuation">(</span><span class="token punctuation">)</span>
    <span class="token punctuation">{</span>
    <span class="token punctuation">}</span>
<span class="token punctuation">}</span>
</code></pre></div><div class="tip custom-block"><p class="custom-block-title">Annotations only?</p><p>If you are only interested in annotations you canleave out the attribute setup line (<code>#[\\Attribute...</code>) for <code>BadRequest</code>.</p><p>Furthermore, your custom annotations should extend from the <code>OpenApi\\Annotations</code> namespace.</p></div><h2 id="annotating-class-constants" tabindex="-1">Annotating class constants <a class="header-anchor" href="#annotating-class-constants" aria-hidden="true">#</a></h2><div class="language-php"><pre><code><span class="token keyword">use</span> <span class="token package">OpenApi<span class="token punctuation">\\</span>Attributes</span> <span class="token keyword">as</span> <span class="token constant">OA</span><span class="token punctuation">;</span>

<span class="token attribute"><span class="token delimiter punctuation">#[</span><span class="token attribute-content"><span class="token attribute-class-name class-name class-name-fully-qualified">OA<span class="token punctuation">\\</span>Schema</span><span class="token punctuation">(</span><span class="token punctuation">)</span></span><span class="token delimiter punctuation">]</span></span>
<span class="token keyword">class</span> <span class="token class-name-definition class-name">Airport</span>
<span class="token punctuation">{</span>
    <span class="token attribute"><span class="token delimiter punctuation">#[</span><span class="token attribute-content"><span class="token attribute-class-name class-name class-name-fully-qualified">OA<span class="token punctuation">\\</span>Property</span><span class="token punctuation">(</span><span class="token attribute-class-name class-name">property</span><span class="token operator">=</span><span class="token string single-quoted-string">&#39;kind&#39;</span><span class="token punctuation">)</span></span><span class="token delimiter punctuation">]</span></span>
    <span class="token keyword">public</span> <span class="token keyword">const</span> <span class="token constant">KIND</span> <span class="token operator">=</span> <span class="token string single-quoted-string">&#39;Airport&#39;</span><span class="token punctuation">;</span>
<span class="token punctuation">}</span>
</code></pre></div><p>The <code>const</code> property is supported in OpenApi 3.1.0.</p><div class="language-yaml"><pre><code><span class="token key atrule">components</span><span class="token punctuation">:</span>
  <span class="token key atrule">schemas</span><span class="token punctuation">:</span>
    <span class="token key atrule">Airport</span><span class="token punctuation">:</span>
        <span class="token key atrule">properties</span><span class="token punctuation">:</span>
          <span class="token key atrule">kind</span><span class="token punctuation">:</span>
            <span class="token key atrule">type</span><span class="token punctuation">:</span> string
            <span class="token key atrule">const</span><span class="token punctuation">:</span> Airport
</code></pre></div><p>For 3.0.0 this is serialized into a single value <code>enum</code>.</p><div class="language-yaml"><pre><code><span class="token key atrule">components</span><span class="token punctuation">:</span>
  <span class="token key atrule">schemas</span><span class="token punctuation">:</span>
    <span class="token key atrule">Airport</span><span class="token punctuation">:</span>
        <span class="token key atrule">properties</span><span class="token punctuation">:</span>
          <span class="token key atrule">kind</span><span class="token punctuation">:</span>
            <span class="token key atrule">type</span><span class="token punctuation">:</span> string
            <span class="token key atrule">enum</span><span class="token punctuation">:</span>
              <span class="token punctuation">-</span> Airport
</code></pre></div>`,65);function h(y,q,g,f,b,A){const t=u("codeblock");return l(),p("div",null,[r,c(t,{id:"minimal"},{an:a(()=>[d]),at:a(()=>[k]),_:1}),m])}var w=o(i,[["render",h]]);export{O as __pageData,w as default};
