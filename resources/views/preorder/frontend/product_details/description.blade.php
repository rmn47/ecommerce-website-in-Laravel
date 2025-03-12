<div class="bg-white mb-4 border p-3 p-sm-4 rounded-2">
    <!-- Tabs -->
    <div class="nav aiz-nav-tabs">
        <a href="#tab_default_1" data-toggle="tab"
            class="mr-5 pb-2 fs-16 fw-700 text-reset active show">{{ translate('Description') }}</a>
        @if ($product->video_link != null)
            <a href="#tab_default_2" data-toggle="tab"
                class="mr-5 pb-2 fs-16 fw-700 text-reset">{{ translate('Video') }}</a>
        @endif
    </div>

    <!-- Description -->
    <div class="tab-content pt-0">
        <!-- Description -->
        <div class="tab-pane fade active show" id="tab_default_1">
            <div class="py-5">
                <div class="mw-100 overflow-hidden text-left aiz-editor-data">
                    <?php echo $product->description; ?>
                </div>
            </div>
        </div>

        <!-- Video -->
        <div class="tab-pane fade" id="tab_default_2">
            <div class="py-5">
                <div class="embed-responsive embed-responsive-16by9">
                    @if ($product->video_provider == 'youtube' && isset(explode('=', $product->video_link)[1]))
                        <iframe class="embed-responsive-item"
                            src="https://www.youtube.com/embed/{{ get_url_params($product->video_link, 'v') }}"></iframe>
                    @elseif ($product->video_provider == 'dailymotion' && isset(explode('video/', $product->video_link)[1]))
                        <iframe class="embed-responsive-item"
                            src="https://www.dailymotion.com/embed/video/{{ explode('video/', $product->video_link)[1] }}"></iframe>
                    @elseif ($product->video_provider == 'vimeo' && isset(explode('vimeo.com/', $product->video_link)[1]))
                        <iframe
                            src="https://player.vimeo.com/video/{{ explode('vimeo.com/', $product->video_link)[1] }}"
                            width="500" height="281" frameborder="0" webkitallowfullscreen
                            mozallowfullscreen allowfullscreen></iframe>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Download -->
        <div class="tab-pane fade" id="tab_default_3">
            <div class="py-5 text-center ">
                <a href="{{ uploaded_asset($product->pdf) }}"
                    class="btn btn-primary">{{ translate('Download') }}</a>
            </div>
        </div>
    </div>
</div>