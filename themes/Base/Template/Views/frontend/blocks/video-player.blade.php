<div >
    <div class="bravo_gallery w-100" >
        <div class="btn-group w-100">
            <span class="btn-transparent has-icon bravo-video-popup w-100" @if($youtube) data-toggle="modal" @endif data-src="{{ str_ireplace("watch?v=","embed/",$youtube) }}" data-target="#video-{{$id}}">
                <video width="100%" muted autoplay loop>
                    <source src="{{asset('dist/frontend/imkan.mp4')}}" type="video/mp4">
                    <source src="{{asset('dist/frontend/imkan.mp4')}}" type="video/ogg">
                   </video> 
            </span>
        </div>
        @if($youtube)
            <!--<div class="modal fade" id="video-{{$id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">-->
            <!--    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">-->
            <!--        <div class="modal-content p-0">-->
            <!--            <div class="modal-body">-->
            <!--                <div class="embed-responsive embed-responsive-16by9">-->
            <!--                    <iframe class="embed-responsive-item bravo_embed_video" src="{{ handleVideoUrl($youtube) }}" allowscriptaccess="always" allow="autoplay"></iframe>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
        @endif
    </div>
</div>
