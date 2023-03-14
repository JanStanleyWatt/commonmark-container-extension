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

namespace JSW\Container;

use JSW\Container\Delimiter\ContainerDelimiterProcesser;
use JSW\Container\Node\Division;
use JSW\Container\Node\Span;
use JSW\Container\Parser\ContainerParser;
use JSW\Container\Renderer\DivisionRenderer;
use JSW\Container\Renderer\SpanRenderer;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\Config\ConfigurationBuilderInterface;
use Nette\Schema\Expect;

final class ContainerExtension implements ConfigurableExtensionInterface
{
    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        $builder->addSchema('container',
            Expect::structure([
                'default_class_name' => Expect::string(),
            ])
        );
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addBlockStartParser(ContainerParser::createBlockStartParser())
                    ->addDelimiterProcessor(new ContainerDelimiterProcesser())
                    ->addRenderer(Division::class, new DivisionRenderer())
                    ->addRenderer(Span::class, new SpanRenderer());
    }
}
