<?php

namespace NomadTaxCalc\Theme\Classes\Features;

use NomadTaxCalc\Theme\Traits\Singleton;

class ReadingProgressBar
{
    use Singleton;

    protected function __construct()
    {
        add_action('wp_footer', [$this, 'render_progress_bar']);
    }

    /**
     * Render the progress bar HTML
     * 
     * @return void
     */
    public function render_progress_bar()
    {
        if (!is_single()) {
            return;
        }

        ?>
        <div id="reading-progress-container" class="reading-progress-container">
            <div id="reading-progress-bar" class="reading-progress-bar"></div>
        </div>
        <?php
    }
}
