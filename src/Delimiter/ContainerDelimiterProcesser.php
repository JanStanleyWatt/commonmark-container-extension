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

namespace JSW\Container\Delimiter;

use JSW\Container\Node\Span;
use League\CommonMark\Delimiter\DelimiterInterface;
use League\CommonMark\Delimiter\Processor\DelimiterProcessorInterface;
use League\CommonMark\Node\Inline\AbstractStringContainer;

final class ContainerDelimiterProcesser implements DelimiterProcessorInterface
{
    public function process(AbstractStringContainer $opener, AbstractStringContainer $closer, int $delimiterUse): void
    {
        $node = new Span();
        $next = $opener->next();
        while (null !== $next && $next !== $closer) {
            $tmp = $next->next();
            $node->appendChild($next);
            $next = $tmp;
        }

        $opener->insertAfter($node);
    }

    public function getOpeningCharacter(): string
    {
        return ':';
    }

    public function getClosingCharacter(): string
    {
        return ':';
    }

    public function getMinLength(): int
    {
        return 2;
    }

    public function getDelimiterUse(DelimiterInterface $opener, DelimiterInterface $closer): int
    {
        return 2;
    }
}
