<?php

/**
 * Copyright 2023 Jan stanray watt

 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at

 *  http://www.apache.org/licenses/LICENSE-2.0

 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace JSW\Container\Parser;

use JSW\Container\Node\Division;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;

final class ContainerParser extends AbstractBlockContinueParser
{
    private Division $block;
    private string $delim;
    private string $class_name;

    public function __construct(string $class_name, string $delim)
    {
        $this->block = new Division();
        $this->delim = $delim;
        $this->class_name = $class_name;
    }

    public static function createBlockStartParser(): BlockStartParserInterface
    {
        return new class() implements BlockStartParserInterface {
            public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parserState): ?BlockStart
            {
                if (':' !== $cursor->getNextNonSpaceCharacter()) {
                    return BlockStart::none();
                }

                $match = $cursor->match('/^[\s\t]*:{3,}/u');

                if (null === $match) {
                    return BlockStart::none();
                }

                $class_name = '';
                if (false === $cursor->isAtEnd()) {
                    $class_name = $cursor->getRemainder();
                    $cursor->advanceToEnd();
                }

                return BlockStart::of(new ContainerParser($class_name, $match))->at($cursor);
            }
        };
    }

    public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue
    {
        $cursor->advanceToNextNonSpaceOrTab();

        if ($this->delim === $cursor->getRemainder()) {
            return BlockContinue::finished();
        }

        return BlockContinue::at($cursor);
    }

    public function closeBlock(): void
    {
        $this->block->data->set('class_name', $this->class_name);
    }

    public function getBlock(): AbstractBlock
    {
        return $this->block;
    }

    public function isContainer(): bool
    {
        return true;
    }

    public function canContain(AbstractBlock $childBlock): bool
    {
        return true;
    }
}
