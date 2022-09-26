import{_ as s,c as a,o as n,a as p}from"./app.648f5e92.js";const i=JSON.parse('{"title":"Create a page","description":"","frontmatter":{},"headers":[{"level":2,"title":"Empty page","slug":"empty-page"},{"level":2,"title":"Page with content","slug":"page-with-content"}],"relativePath":"how-to/create-a-page.md","lastUpdated":1664224113000}'),o={name:"how-to/create-a-page.md"},l=p(`<h1 id="create-a-page" tabindex="-1">Create a page <a class="header-anchor" href="#create-a-page" aria-hidden="true">#</a></h1><h2 id="empty-page" tabindex="-1">Empty page <a class="header-anchor" href="#empty-page" aria-hidden="true">#</a></h2><div class="language-php"><span class="copy"></span><pre><code><span class="line"><span style="color:#89DDFF;">&lt;?</span><span style="color:#A6ACCD;">php</span></span>
<span class="line"></span>
<span class="line"><span style="color:#F78C6C;">use</span><span style="color:#FFCB6B;"> </span><span style="color:#A6ACCD;">Notion</span><span style="color:#89DDFF;">\\</span><span style="color:#A6ACCD;">Notion</span><span style="color:#89DDFF;">;</span></span>
<span class="line"><span style="color:#F78C6C;">use</span><span style="color:#FFCB6B;"> </span><span style="color:#A6ACCD;">Notion</span><span style="color:#89DDFF;">\\</span><span style="color:#A6ACCD;">Common</span><span style="color:#89DDFF;">\\</span><span style="color:#A6ACCD;">Emoji</span><span style="color:#89DDFF;">;</span></span>
<span class="line"><span style="color:#F78C6C;">use</span><span style="color:#FFCB6B;"> </span><span style="color:#A6ACCD;">Notion</span><span style="color:#89DDFF;">\\</span><span style="color:#A6ACCD;">Pages</span><span style="color:#89DDFF;">\\</span><span style="color:#A6ACCD;">Page</span><span style="color:#89DDFF;">;</span></span>
<span class="line"><span style="color:#F78C6C;">use</span><span style="color:#FFCB6B;"> </span><span style="color:#A6ACCD;">Notion</span><span style="color:#89DDFF;">\\</span><span style="color:#A6ACCD;">Pages</span><span style="color:#89DDFF;">\\</span><span style="color:#A6ACCD;">PageParent</span><span style="color:#89DDFF;">;</span></span>
<span class="line"></span>
<span class="line"><span style="color:#89DDFF;">$</span><span style="color:#A6ACCD;">token </span><span style="color:#89DDFF;">=</span><span style="color:#A6ACCD;"> </span><span style="color:#89DDFF;">$</span><span style="color:#A6ACCD;">_ENV</span><span style="color:#89DDFF;">[</span><span style="color:#89DDFF;">&quot;</span><span style="color:#C3E88D;">NOTION_SECRET</span><span style="color:#89DDFF;">&quot;</span><span style="color:#89DDFF;">];</span></span>
<span class="line"><span style="color:#89DDFF;">$</span><span style="color:#A6ACCD;">notion </span><span style="color:#89DDFF;">=</span><span style="color:#A6ACCD;"> </span><span style="color:#FFCB6B;">Notion</span><span style="color:#89DDFF;">::</span><span style="color:#82AAFF;">create</span><span style="color:#89DDFF;">($</span><span style="color:#A6ACCD;">token</span><span style="color:#89DDFF;">);</span></span>
<span class="line"></span>
<span class="line"><span style="color:#89DDFF;">$</span><span style="color:#A6ACCD;">parent </span><span style="color:#89DDFF;">=</span><span style="color:#A6ACCD;"> </span><span style="color:#FFCB6B;">PageParent</span><span style="color:#89DDFF;">::</span><span style="color:#82AAFF;">page</span><span style="color:#89DDFF;">(</span><span style="color:#89DDFF;">&quot;</span><span style="color:#C3E88D;">c986d7b0-7051-4f18-b165-cc0b9503ffc2</span><span style="color:#89DDFF;">&quot;</span><span style="color:#89DDFF;">);</span></span>
<span class="line"><span style="color:#89DDFF;">$</span><span style="color:#A6ACCD;">page </span><span style="color:#89DDFF;">=</span><span style="color:#A6ACCD;"> </span><span style="color:#FFCB6B;">Page</span><span style="color:#89DDFF;">::</span><span style="color:#82AAFF;">create</span><span style="color:#89DDFF;">($</span><span style="color:#A6ACCD;">parent</span><span style="color:#89DDFF;">)</span></span>
<span class="line"><span style="color:#A6ACCD;">            </span><span style="color:#89DDFF;">-&gt;</span><span style="color:#82AAFF;">withTitle</span><span style="color:#89DDFF;">(</span><span style="color:#89DDFF;">&quot;</span><span style="color:#C3E88D;">Empty page</span><span style="color:#89DDFF;">&quot;</span><span style="color:#89DDFF;">)</span></span>
<span class="line"><span style="color:#A6ACCD;">            </span><span style="color:#89DDFF;">-&gt;</span><span style="color:#82AAFF;">withIcon</span><span style="color:#89DDFF;">(</span><span style="color:#FFCB6B;">Emoji</span><span style="color:#89DDFF;">::</span><span style="color:#82AAFF;">create</span><span style="color:#89DDFF;">(</span><span style="color:#89DDFF;">&quot;</span><span style="color:#C3E88D;">\u2B50</span><span style="color:#89DDFF;">&quot;</span><span style="color:#89DDFF;">));</span></span>
<span class="line"></span>
<span class="line"><span style="color:#89DDFF;">$</span><span style="color:#A6ACCD;">page </span><span style="color:#89DDFF;">=</span><span style="color:#A6ACCD;"> </span><span style="color:#89DDFF;">$</span><span style="color:#A6ACCD;">notion</span><span style="color:#89DDFF;">-&gt;</span><span style="color:#82AAFF;">pages</span><span style="color:#89DDFF;">()-&gt;</span><span style="color:#82AAFF;">create</span><span style="color:#89DDFF;">($</span><span style="color:#A6ACCD;">page</span><span style="color:#89DDFF;">);</span></span>
<span class="line"></span></code></pre></div><h2 id="page-with-content" tabindex="-1">Page with content <a class="header-anchor" href="#page-with-content" aria-hidden="true">#</a></h2><div class="language-php"><span class="copy"></span><pre><code><span class="line"><span style="color:#89DDFF;">&lt;?</span><span style="color:#A6ACCD;">php</span></span>
<span class="line"></span>
<span class="line"><span style="color:#F78C6C;">use</span><span style="color:#FFCB6B;"> </span><span style="color:#A6ACCD;">Notion</span><span style="color:#89DDFF;">\\</span><span style="color:#A6ACCD;">Blocks</span><span style="color:#89DDFF;">\\</span><span style="color:#A6ACCD;">Heading1</span><span style="color:#89DDFF;">;</span></span>
<span class="line"><span style="color:#F78C6C;">use</span><span style="color:#FFCB6B;"> </span><span style="color:#A6ACCD;">Notion</span><span style="color:#89DDFF;">\\</span><span style="color:#A6ACCD;">Notion</span><span style="color:#89DDFF;">;</span></span>
<span class="line"><span style="color:#F78C6C;">use</span><span style="color:#FFCB6B;"> </span><span style="color:#A6ACCD;">Notion</span><span style="color:#89DDFF;">\\</span><span style="color:#A6ACCD;">Blocks</span><span style="color:#89DDFF;">\\</span><span style="color:#A6ACCD;">ToDo</span><span style="color:#89DDFF;">;</span></span>
<span class="line"><span style="color:#F78C6C;">use</span><span style="color:#FFCB6B;"> </span><span style="color:#A6ACCD;">Notion</span><span style="color:#89DDFF;">\\</span><span style="color:#A6ACCD;">Common</span><span style="color:#89DDFF;">\\</span><span style="color:#A6ACCD;">Emoji</span><span style="color:#89DDFF;">;</span></span>
<span class="line"><span style="color:#F78C6C;">use</span><span style="color:#FFCB6B;"> </span><span style="color:#A6ACCD;">Notion</span><span style="color:#89DDFF;">\\</span><span style="color:#A6ACCD;">Pages</span><span style="color:#89DDFF;">\\</span><span style="color:#A6ACCD;">Page</span><span style="color:#89DDFF;">;</span></span>
<span class="line"><span style="color:#F78C6C;">use</span><span style="color:#FFCB6B;"> </span><span style="color:#A6ACCD;">Notion</span><span style="color:#89DDFF;">\\</span><span style="color:#A6ACCD;">Pages</span><span style="color:#89DDFF;">\\</span><span style="color:#A6ACCD;">PageParent</span><span style="color:#89DDFF;">;</span></span>
<span class="line"></span>
<span class="line"><span style="color:#89DDFF;">$</span><span style="color:#A6ACCD;">token </span><span style="color:#89DDFF;">=</span><span style="color:#A6ACCD;"> </span><span style="color:#89DDFF;">$</span><span style="color:#A6ACCD;">_ENV</span><span style="color:#89DDFF;">[</span><span style="color:#89DDFF;">&quot;</span><span style="color:#C3E88D;">NOTION_SECRET</span><span style="color:#89DDFF;">&quot;</span><span style="color:#89DDFF;">];</span></span>
<span class="line"><span style="color:#89DDFF;">$</span><span style="color:#A6ACCD;">notion </span><span style="color:#89DDFF;">=</span><span style="color:#A6ACCD;"> </span><span style="color:#FFCB6B;">Notion</span><span style="color:#89DDFF;">::</span><span style="color:#82AAFF;">create</span><span style="color:#89DDFF;">($</span><span style="color:#A6ACCD;">token</span><span style="color:#89DDFF;">);</span></span>
<span class="line"></span>
<span class="line"><span style="color:#89DDFF;">$</span><span style="color:#A6ACCD;">parent </span><span style="color:#89DDFF;">=</span><span style="color:#A6ACCD;"> </span><span style="color:#FFCB6B;">PageParent</span><span style="color:#89DDFF;">::</span><span style="color:#82AAFF;">page</span><span style="color:#89DDFF;">(</span><span style="color:#89DDFF;">&quot;</span><span style="color:#C3E88D;">c986d7b0-7051-4f18-b165-cc0b9503ffc2</span><span style="color:#89DDFF;">&quot;</span><span style="color:#89DDFF;">);</span></span>
<span class="line"><span style="color:#89DDFF;">$</span><span style="color:#A6ACCD;">page </span><span style="color:#89DDFF;">=</span><span style="color:#A6ACCD;"> </span><span style="color:#FFCB6B;">Page</span><span style="color:#89DDFF;">::</span><span style="color:#82AAFF;">create</span><span style="color:#89DDFF;">($</span><span style="color:#A6ACCD;">parent</span><span style="color:#89DDFF;">)</span></span>
<span class="line"><span style="color:#A6ACCD;">            </span><span style="color:#89DDFF;">-&gt;</span><span style="color:#82AAFF;">withTitle</span><span style="color:#89DDFF;">(</span><span style="color:#89DDFF;">&quot;</span><span style="color:#C3E88D;">Shopping list</span><span style="color:#89DDFF;">&quot;</span><span style="color:#89DDFF;">)</span></span>
<span class="line"><span style="color:#A6ACCD;">            </span><span style="color:#89DDFF;">-&gt;</span><span style="color:#82AAFF;">withIcon</span><span style="color:#89DDFF;">(</span><span style="color:#FFCB6B;">Emoji</span><span style="color:#89DDFF;">::</span><span style="color:#82AAFF;">create</span><span style="color:#89DDFF;">(</span><span style="color:#89DDFF;">&quot;</span><span style="color:#C3E88D;">\u{1F6D2}</span><span style="color:#89DDFF;">&quot;</span><span style="color:#89DDFF;">));</span></span>
<span class="line"></span>
<span class="line"><span style="color:#89DDFF;">$</span><span style="color:#A6ACCD;">content </span><span style="color:#89DDFF;">=</span><span style="color:#A6ACCD;"> </span><span style="color:#89DDFF;">[</span></span>
<span class="line"><span style="color:#A6ACCD;">    </span><span style="color:#FFCB6B;">Heading1</span><span style="color:#89DDFF;">::</span><span style="color:#82AAFF;">fromString</span><span style="color:#89DDFF;">(</span><span style="color:#89DDFF;">&quot;</span><span style="color:#C3E88D;">Supermarket</span><span style="color:#89DDFF;">&quot;</span><span style="color:#89DDFF;">),</span></span>
<span class="line"><span style="color:#A6ACCD;">    </span><span style="color:#FFCB6B;">ToDo</span><span style="color:#89DDFF;">::</span><span style="color:#82AAFF;">fromString</span><span style="color:#89DDFF;">(</span><span style="color:#89DDFF;">&quot;</span><span style="color:#C3E88D;">Tomato</span><span style="color:#89DDFF;">&quot;</span><span style="color:#89DDFF;">),</span></span>
<span class="line"><span style="color:#A6ACCD;">    </span><span style="color:#FFCB6B;">ToDo</span><span style="color:#89DDFF;">::</span><span style="color:#82AAFF;">fromString</span><span style="color:#89DDFF;">(</span><span style="color:#89DDFF;">&quot;</span><span style="color:#C3E88D;">Sugar</span><span style="color:#89DDFF;">&quot;</span><span style="color:#89DDFF;">),</span></span>
<span class="line"><span style="color:#A6ACCD;">    </span><span style="color:#FFCB6B;">ToDo</span><span style="color:#89DDFF;">::</span><span style="color:#82AAFF;">fromString</span><span style="color:#89DDFF;">(</span><span style="color:#89DDFF;">&quot;</span><span style="color:#C3E88D;">Apple</span><span style="color:#89DDFF;">&quot;</span><span style="color:#89DDFF;">),</span></span>
<span class="line"><span style="color:#A6ACCD;">    </span><span style="color:#FFCB6B;">ToDo</span><span style="color:#89DDFF;">::</span><span style="color:#82AAFF;">fromString</span><span style="color:#89DDFF;">(</span><span style="color:#89DDFF;">&quot;</span><span style="color:#C3E88D;">Milk</span><span style="color:#89DDFF;">&quot;</span><span style="color:#89DDFF;">),</span></span>
<span class="line"><span style="color:#A6ACCD;">    </span><span style="color:#FFCB6B;">Heading1</span><span style="color:#89DDFF;">::</span><span style="color:#82AAFF;">fromString</span><span style="color:#89DDFF;">(</span><span style="color:#89DDFF;">&quot;</span><span style="color:#C3E88D;">Mall</span><span style="color:#89DDFF;">&quot;</span><span style="color:#89DDFF;">),</span></span>
<span class="line"><span style="color:#A6ACCD;">    </span><span style="color:#FFCB6B;">ToDo</span><span style="color:#89DDFF;">::</span><span style="color:#82AAFF;">fromString</span><span style="color:#89DDFF;">(</span><span style="color:#89DDFF;">&quot;</span><span style="color:#C3E88D;">Black T-shirt</span><span style="color:#89DDFF;">&quot;</span><span style="color:#89DDFF;">),</span></span>
<span class="line"><span style="color:#89DDFF;">];</span></span>
<span class="line"></span>
<span class="line"><span style="color:#89DDFF;">$</span><span style="color:#A6ACCD;">page </span><span style="color:#89DDFF;">=</span><span style="color:#A6ACCD;"> </span><span style="color:#89DDFF;">$</span><span style="color:#A6ACCD;">notion</span><span style="color:#89DDFF;">-&gt;</span><span style="color:#82AAFF;">pages</span><span style="color:#89DDFF;">()-&gt;</span><span style="color:#82AAFF;">create</span><span style="color:#89DDFF;">($</span><span style="color:#A6ACCD;">page</span><span style="color:#89DDFF;">,</span><span style="color:#A6ACCD;"> </span><span style="color:#89DDFF;">$</span><span style="color:#A6ACCD;">content</span><span style="color:#89DDFF;">);</span></span>
<span class="line"></span></code></pre></div>`,5),e=[l];function t(F,c,D,r,y,C){return n(),a("div",null,e)}var g=s(o,[["render",t]]);export{i as __pageData,g as default};