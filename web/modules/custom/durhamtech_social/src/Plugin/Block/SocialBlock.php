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
            '#attached' => array(
                'library' => array(
                    'durhamtech_social/slick'
                ),
            ),
            '#markup' => '
            <div class="slick">
                <div><img src="sites/default/files/2018-03/782849_c0fae58e.jpg"/></div>
                <div><img src="sites/default/files/2018-05/action-2277292_640.jpg"/></div>
                <div><img src="sites/default/files/2018-05/DT Engineering THUMBNAIL_1.jpg"/></div>
                <div><img src="sites/default/files/2018-05/golden leaf_0.jpg"/></div>
                <div><img src="sites/default/files/2018-05/momentum THUMBNAIL.jpg"/></div>
                <div><img src="sites/default/files/2018-05/DT Engineering THUMBNAIL_1.jpg"/></div>
            </div>
            ',
        );
    }
}