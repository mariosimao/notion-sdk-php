<?php

namespace Notion\Blocks;

use Notion\Common\RichText;

/**
 * @psalm-import-type BlockJson from Block
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 *
 * @psalm-type CodeJson = array{
 *      code: array{
 *          text: list<RichTextJson>,
 *          language: string,
 *      },
 * }
 *
 * @psalm-immutable
 */
class Code implements BlockInterface
{
    private const TYPE = Block::TYPE_CODE;

    public const LANG_ABAP = "abap";
    public const LANG_ARDUINO = "arduino";
    public const LANG_BASH = "bash";
    public const LANG_BASIC = "basic";
    public const LANG_C = "c";
    public const LANG_CLOJURE = "clojure";
    public const LANG_COFFEESCRIPT = "coffeescript";
    public const LANG_CPP = "c++";
    public const LANG_C_SHARP = "c#";
    public const LANG_CSS = "css";
    public const LANG_DART = "dart";
    public const LANG_DIFF = "diff";
    public const LANG_DOCKER = "docker";
    public const LANG_ELIXIR = "elixir";
    public const LANG_ELM = "elm";
    public const LANG_ERLANG = "erlang";
    public const LANG_FLOW = "flow";
    public const LANG_FORTRAN = "fortran";
    public const LANG_F_SHARP = "f#";
    public const LANG_GHERKIN = "gherkin";
    public const LANG_GLSL = "glsl";
    public const LANG_GO = "go";
    public const LANG_GRAPHQL = "graphql";
    public const LANG_GROOVY = "groovy";
    public const LANG_HASKELL = "haskell";
    public const LANG_HTML = "html";
    public const LANG_JAVA = "java";
    public const LANG_JAVASCRIPT = "javaScript";
    public const LANG_JSON = "json";
    public const LANG_JULIA = "julia";
    public const LANG_KOTLIN = "kotlin";
    public const LANG_LATEX = "latex";
    public const LANG_LESS = "less";
    public const LANG_LISP = "lisp";
    public const LANG_LIVESCRIPT = "livescript";
    public const LANG_LUA = "lua";
    public const LANG_MAKEFILE = "makefile";
    public const LANG_MARKDOWN = "markdown";
    public const LANG_MARKUP = "markup";
    public const LANG_MATLAB = "matlab";
    public const LANG_MERMAID = "mermaid";
    public const LANG_NIX = "nix";
    public const LANG_OBJECTIVE_C = "objective-c";
    public const LANG_OCAML = "ocaml";
    public const LANG_PASCAL = "pascal";
    public const LANG_PERL = "perl";
    public const LANG_PHP = "php";
    public const LANG_PLAIN_TEXT = "plain text";
    public const LANG_POWERSHELL = "powershell";
    public const LANG_PROLOG = "prolog";
    public const LANG_PROTOBUF = "protobuf";
    public const LANG_PYTHON = "python";
    public const LANG_R = "r";
    public const LANG_REASON = "reason";
    public const LANG_RUBY = "ruby";
    public const LANG_RUST = "rust";
    public const LANG_SASS = "sass";
    public const LANG_SCALA = "scala";
    public const LANG_SCHEME = "scheme";
    public const LANG_SCSS = "scss";
    public const LANG_SHELL = "shell";
    public const LANG_SQL = "sql";
    public const LANG_SWIFT = "swift";
    public const LANG_TYPESCRIPT = "typescript";
    public const LANG_VB_NET = "vb.net";
    public const LANG_VERILOG = "verilog";
    public const LANG_VHDL = "vhdl";
    public const LANG_VISUAL_BASIC = "visual basic";
    public const LANG_WEBASSEMBLY = "webassembly";
    public const LANG_XML = "xml";
    public const LANG_YAML = "yaml";

    private Block $block;

    /** @var list<RichText> */
    private array $text;

    private string $language;

    /** @param list<RichText> $text */
    private function __construct(Block $block, array $text, string $language)
    {
        if (!$block->isCode()) {
            throw new \Exception("Block must be of type " . self::TYPE);
        }

        $this->block = $block;
        $this->text = $text;
        $this->language = $language;
    }

    public static function create(
        string $code = "",
        string $language = self::LANG_PLAIN_TEXT
    ): self {
        $block = Block::create(self::TYPE);
        $text = [];
        if ($code !== "") {
            $text[] = RichText::createText($code) ;
        }

        return new self($block, $text, $language);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockJson $array */
        $block = Block::fromArray($array);

        /** @psalm-var CodeJson $array */
        $code = $array[self::TYPE];
        $text = array_map(fn($t) => RichText::fromArray($t), $code["text"]);
        $language = $code["language"];

        return new self($block, $text, $language);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = $this->block->toArray();

        $array[self::TYPE] = [
            "text"     => array_map(fn(RichText $t) => $t->toArray(), $this->text),
            "language" => $this->language,
        ];

        return $array;
    }

    public function toString(): string
    {
        $string = "";
        foreach ($this->text as $richText) {
            $string = $string . $richText->plainText();
        }

        return $string;
    }

    public function block(): Block
    {
        return $this->block;
    }

    public function text(): array
    {
        return $this->text;
    }

    public function language(): string
    {
        return $this->language;
    }

    /** @param list<RichText> $text */
    public function withText(array $text): self
    {
        return new self($this->block, $text, $this->language);
    }

    public function appendText(RichText $text): self
    {
        $texts = $this->text;
        $texts[] = $text;

        return new self($this->block, $texts, $this->language);
    }

    public function withLanguage(string $language): self
    {
        return new self($this->block, $this->text, $language);
    }
}
