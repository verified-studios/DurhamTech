<?php

namespace Drupal\durhamtech_social\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'social' block.
 *
 * @Block(
 *   id = "durhamtech_social_block",
 *   admin_label = @Translation("Durham Tech Social Media Slider"),
 *   category = @Translation("Durham Tech")
 * )
 */
class SocialBlock extends BlockBase
{
    /**
     * {@inheritdoc}
     */
    public function build()
    {
        return array(
            '#type' => 'markup',
            '#attached' => array (
              'library' => array(
                  'durhamtech_social/slick'
              ),
            ),
            '#markup' => '
            <div class="slick">
                <div>test</div>
                <div>test</div>
                <div>test</div>
            </div>
            ',
        );
    }

}