@extends('frontend.layouts.master')
@section('meta_title', 'Blog | Aashi Retail')
@section('meta_description', 'Read the Aashi Retail blog for tips, guides and stories about rainwear, windcheaters, winter wear and bags.')

@section('title','Aashi-Ecommerce || Blog Page')

@section('main-content')
    <h1 class="sr-only">Blog</h1>
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{route('home')}}">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0);">Blog</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Start Blog Single -->
    <section class="blog-single shop-blog grid section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-12">
                    <div class="row">
                        @foreach($posts as $post)
                        {{-- {{$post}} --}}
                            <div class="col-lg-4 col-md-4 col-12">
                                <!-- Start Single Blog  -->
                                <div class="shop-single-blog">
                                <a href="{{ route('blog.detail', $post->slug) }}" class="blog-img-link"><img src="{{asset('public' . $post->photo)}}" alt="{{ $post->title }}"></a>
                                    <div class="content">
                                        <p class="date"><i class="fa fa-calendar" aria-hidden="true"></i> {{$post->created_at->format('d M, Y. D')}}
                                            <span class="float-right">
                                                <i class="fa fa-user" aria-hidden="true"></i>
                                                 {{$post->author_info->name ?? 'Anonymous'}}
                                            </span>
                                        </p>
                                        <a href="{{route('blog.detail',$post->slug)}}" class="title">{{$post->title}}</a>
                                        <p>{!! html_entity_decode($post->summary) !!}</p>
                                        <a href="{{route('blog.detail',$post->slug)}}" class="more-btn">Continue Reading</a>
                                    </div>
                                </div>
                                <!-- End Single Blog  -->
                            </div>
                        @endforeach
                        <div class="col-12">
                            <!-- Pagination -->
                            {{-- {{$posts->appends($_GET)->links()}} --}}
                            <!--/ End Pagination -->
                        </div>
                    </div>
                </div>
                   
            </div>
        </div>
    </section>
    <!--/ End Blog Single -->
@endsection
@push('styles')
    <style>
        .pagination{
            display:inline-flex;
        }

        /* Blog cards: the image is a link, and the card reacts on hover */
        .shop-blog .shop-single-blog {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.35s ease, box-shadow 0.35s ease;
        }

        .shop-blog .shop-single-blog .content {
            padding: 22px 18px;
        }

        .shop-blog .blog-img-link {
            display: block;
            overflow: hidden;
        }

        .shop-blog .blog-img-link img {
            display: block;
            transition: transform 0.5s ease;
        }

        .shop-blog .shop-single-blog:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 30px -10px rgba(0, 0, 0, 0.25);
        }

        .shop-blog .shop-single-blog:hover .blog-img-link img {
            transform: scale(1.06);
        }

        .shop-blog .shop-single-blog:hover .content .title {
            color: #5db845;
        }

        .shop-blog .shop-single-blog .content .more-btn {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .shop-blog .shop-single-blog:hover .content .more-btn {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px -6px rgba(93, 184, 69, 0.7);
        }

        /* People who prefer less motion get no movement */
        @media (prefers-reduced-motion: reduce) {
            .shop-blog .shop-single-blog,
            .shop-blog .blog-img-link img,
            .shop-blog .shop-single-blog .content .more-btn {
                transition: none;
            }

            .shop-blog .shop-single-blog:hover,
            .shop-blog .shop-single-blog:hover .blog-img-link img,
            .shop-blog .shop-single-blog:hover .content .more-btn {
                transform: none;
            }
        }
    </style>

@endpush
