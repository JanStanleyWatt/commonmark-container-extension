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

namespace JSW\Container\Renderer;

use JSW\Container\Node\Division;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use League\CommonMark\Xml\XmlNodeRendererInterface;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;

final class ContainerRenderer implements NodeRendererInterface, XmlNodeRendererInterface, ConfigurationAwareInterface
{
    private ConfigurationInterface $config;
    private string $class_name = '';

    /**
     * @param Division $node
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer)
    {
        Division::assertInstanceOf($node);

        $this->class_name = $this->config->get('container/default_class_name') ?? '';

        $class_name = $node->data->get('class_name', '');
        if ('' !== $class_name) {
            $this->class_name = $class_name;
        }

        $attrs = $node->data->get('attributes');
        if ('' !== $this->class_name) {
            $attrs['class'] = $this->class_name;
        }

        return new HtmlElement('div', $attrs, $childRenderer->renderNodes($node->children()));
    }

    /**
     * @param Division $node
     */
    public function getXmlTagName(Node $node): string
    {
        return 'div';
    }

    /**
     * @param Division $node
     */
    public function getXmlAttributes(Node $node): array
    {
        return '' !== $this->class_name ? ['class' => $this->class_name] : [];
    }

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->config = $configuration;
    }
}
