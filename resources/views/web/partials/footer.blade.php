<footer class="page-footer font-small pt-4">
    <div class="container text-center text-md-left">
        <div class="row ">
            <div class="col-md mx-auto">
                <h5 class="mt-3 mb-4">Footer Content:</h5>
                <p>Here you can use rows and columns here to organize your footer content. Lorem
                    ipsum dolor sit amet, consectetur
                    adipisicing elit.</p>
            </div>
            <hr class="clearfix w-100 d-md-none">
            <div class="col-md mx-auto">
                @render('icore::newsletterComponent')
            </div>
            <hr class="clearfix w-100 d-md-none">
            <div class="col-md-auto mx-auto">
                @render('icore::linkComponent', ['max' => 5, 'cats' => $catsAsArray ?? null])
            </div>
            <hr class="clearfix w-100 d-md-none">
        </div>
        <h5 class="mt-3 mb-2">{{ trans('icore::pages.map') }}:</h5>
        <div class="row">
            {{-- @render('icore::page.footerComponent', ['pattern' => [[18, 19, 32], [45], [3, 1]]]) --}}
            @render('icore::page.footerComponent', ['cols' => 3])
            <div class="col-md-3 col-sm-6">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <a href="{{ route('web.post.index') }}"
                        class="@isUrl(route('web.post.index'), 'font-weight-bold')">
                            {{ trans('icore::posts.route.blog') }}
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="{{ route('web.contact.show') }}"
                        class="@isUrl(route('web.contact.show'), 'font-weight-bold')">
                            {{ trans('icore::contact.route.show') }}
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="{{ route('web.friend.index') }}"
                        class="@isUrl(route('web.friend.index'), 'font-weight-bold')">
                            {{ trans('icore::friends.route.index') }}
                        </a>
                    </li>                     
                </ul>
            </div>
        </div>   
        <hr>
        <div class="d-flex justify-content-center">
            <div class="footer-copyright text-center py-3 mr-3">
                <small>
                    2019-{{ now()->year }} Copyright © <a href="">iCore v{{ config('icore.version') }}</a> by Mariusz Wysokiński
                </small>
            </div>
            <div class="btn-group my-auto" id="themeToggle" role="group" aria-label="Zmień motyw">
                <button type="button" class="btn btn-sm btn-light border" style="width:80px;"
                @isTheme(['', null], 'disabled')>{{ trans('icore::default.light') }}</button>
                <button type="button" class="btn btn-sm btn-dark border" style="width:80px;"
                @isTheme('dark', 'disabled')>{{ trans('icore::default.dark') }}</button>
            </div>
        </div>
    </div>
</footer>
