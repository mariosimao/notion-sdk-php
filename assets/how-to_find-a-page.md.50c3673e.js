import{_ as s,c as a,o as n,a as p}from"./app.b02bd6b1.js";const C=JSON.parse('{"title":"Find a page","description":"","frontmatter":{},"headers":[],"relativePath":"how-to/find-a-page.md","lastUpdated":1658010486000}'),o={name:"how-to/find-a-page.md"},l=p(`<h1 id="find-a-page" tabindex="-1">Find a page <a class="header-anchor" href="#find-a-page" aria-hidden="true">#</a></h1><p>It is possible to retrieve a page by knowing its ID.</p><div class="language-php"><span class="copy"></span><pre><code><span class="line"><span style="color:#89DDFF;">&lt;?</span><span style="color:#A6ACCD;">php</span></span>
<span class="line"></span>
<span class="line"><span style="color:#F78C6C;">use</span><span style="color:#FFCB6B;"> </span><span style="color:#A6ACCD;">Notion</span><span style="color:#89DDFF;">\\</span><span style="color:#A6ACCD;">Notion</span><span style="color:#89DDFF;">;</span></span>
<span class="line"></span>
<span class="line"><span style="color:#89DDFF;">$</span><span style="color:#A6ACCD;">token </span><span style="color:#89DDFF;">=</span><span style="color:#A6ACCD;"> </span><span style="color:#89DDFF;">$</span><span style="color:#A6ACCD;">_ENV</span><span style="color:#89DDFF;">[</span><span style="color:#89DDFF;">&quot;</span><span style="color:#C3E88D;">NOTION_SECRET</span><span style="color:#89DDFF;">&quot;</span><span style="color:#89DDFF;">];</span></span>
<span class="line"><span style="color:#89DDFF;">$</span><span style="color:#A6ACCD;">notion </span><span style="color:#89DDFF;">=</span><span style="color:#A6ACCD;"> </span><span style="color:#FFCB6B;">Notion</span><span style="color:#89DDFF;">::</span><span style="color:#82AAFF;">create</span><span style="color:#89DDFF;">($</span><span style="color:#A6ACCD;">token</span><span style="color:#89DDFF;">);</span></span>
<span class="line"></span>
<span class="line"><span style="color:#89DDFF;">$</span><span style="color:#A6ACCD;">pageId </span><span style="color:#89DDFF;">=</span><span style="color:#A6ACCD;"> </span><span style="color:#89DDFF;">&quot;</span><span style="color:#C3E88D;">c986d7b0-7051-4f18-b165-cc0b9503ffc2</span><span style="color:#89DDFF;">&quot;</span><span style="color:#89DDFF;">;</span></span>
<span class="line"><span style="color:#89DDFF;">$</span><span style="color:#A6ACCD;">page </span><span style="color:#89DDFF;">=</span><span style="color:#A6ACCD;"> </span><span style="color:#89DDFF;">$</span><span style="color:#A6ACCD;">notion</span><span style="color:#89DDFF;">-&gt;</span><span style="color:#82AAFF;">pages</span><span style="color:#89DDFF;">()-&gt;</span><span style="color:#82AAFF;">find</span><span style="color:#89DDFF;">($</span><span style="color:#A6ACCD;">pageId</span><span style="color:#89DDFF;">);</span></span>
<span class="line"></span>
<span class="line"><span style="color:#82AAFF;">echo</span><span style="color:#A6ACCD;"> </span><span style="color:#89DDFF;">$</span><span style="color:#A6ACCD;">page</span><span style="color:#89DDFF;">-&gt;</span><span style="color:#82AAFF;">title</span><span style="color:#89DDFF;">()-&gt;</span><span style="color:#82AAFF;">toString</span><span style="color:#89DDFF;">();</span></span>
<span class="line"></span></code></pre></div>`,3),e=[l];function t(c,r,D,F,y,i){return n(),a("div",null,e)}var d=s(o,[["render",t]]);export{C as __pageData,d as default};
