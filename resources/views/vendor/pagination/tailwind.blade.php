<style>
    .pagination-class .page-item {
        list-style: none;
        display: flex;
    }

    .pagination-class {
        position: relative;
        top: 10px;
    }

    .pagination-class .page-link {
        width: 35px;
        height: 35px;
        display: inline-block;
        text-align: center;
        line-height: 19px;
        font-size: 15px;
        color: #000;
        font-weight: 600;
        margin: 0 5px;
        border-radius: 50px;
        z-index: 1;
        text-decoration: none;
        padding: 0.5rem 0;
    }

    .pagination-class .page-active-link {
        background: #8d191a;
        color: #fff;
    }

    .page-item:first-child .page-link {
        margin-left: 0;
        border-top-left-radius: 50px;
        border-bottom-left-radius: 50px;
    }

    .page-item:last-child .page-link {
        border-top-right-radius: 50px;
        border-bottom-right-radius: 50px;
    }

    .pagination-class .pagination-text {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        justify-content: start;
    }

    @media (max-width: 470px) {
        nav.pagination-class .pagination-flex {
            flex-direction: column !important;
        }
    }

    @media (max-width:576px) {
        .pagination-class .page-link {
            width: 32px;
            height: 32px;
            line-height: 18px;
            font-size: 14px;
            margin: 0 2px;
        }
    }
</style>

<?php
$twtCurrentPage = $paginator->currentPage();
?>
@if ($paginator->hasPages())
<div class="pagination-items">
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between 12334 pagination-class">
        <?php /* 
    <div class="flex justify-between flex-1 sm:hidden">
        @if ($paginator->onFirstPage())
        <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">
            {!! __('pagination.previous') !!}
        </span>
        @else
        <a href="javascript:void(0)" onclick="createPagination({{ ( $twtCurrentPage - 1 )}})" data-page="{{ ( $twtCurrentPage - 1 )}}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
            {!! __('pagination.previous') !!}
        </a>
        @endif

        @if ($paginator->hasMorePages())
        <a href="javascript:void(0)" onclick="createPagination({{ ( $twtCurrentPage + 1 )}})" data-page="{{ ( $twtCurrentPage + 1 )}}" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
            {!! __('pagination.next') !!}
        </a>
        @else
        <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">
            {!! __('pagination.next') !!}
        </span>
        @endif
    </div>
    */ ?>
        <div class="pagination-flex card-body d-flex justify-content-between align-items-center">
            <div class="text-center">
                <p class="text-sm text-gray-700 leading-5 mr-auto mb-0">
                    {!! __('Showing') !!}
                    @if ($paginator->firstItem())
                    <span class="font-medium">{{ $paginator->firstItem() }}</span>
                    {!! __('to') !!}
                    <span class="font-medium">{{ $paginator->lastItem() }}</span>
                    @else
                    {{ $paginator->count() }}
                    @endif
                    {!! __('of') !!}
                    <span class="font-medium">{{ $paginator->total() }}</span>
                    {!! __('results') !!}
                </p>
            </div>
            <div class="pagination-box">
                <ul class="pagination mb-0">
                    <span class="relative z-0 inline-flex rounded-md pagination-text">
                        {{-- Previous Page Link --}}
                        @if ($paginator->onFirstPage())
                        <li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="createPagination({{ ( $twtCurrentPage - 1 )}})" data-page="{{ ( $twtCurrentPage - 1 )}}">
                                <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                                    <span class="relative inline-flex items-center  py-2 text-sm font-medium text-gray-500 border-gray-300 cursor-default rounded-l-md leading-5" aria-hidden="true">
                                        <i class="fa fa-angle-left" aria-hidden="true"></i>
                                    </span>
                                </span>
                            </a>
                        </li>
                        @else
                        <li class="page-item"> <a href="javascript:void(0)" onclick="createPagination({{ ( $twtCurrentPage - 1 )}})" data-page="{{ ( $twtCurrentPage - 1 )}}" rel="prev" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white  border-gray-300 rounded-l-md leading-5 hover:text-gray-400 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150 page-link" aria-label="{{ __('pagination.previous') }}">
                                <svg class="w-5 h-5 previous-class prev" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                        <li class="page-item"><a href="javascript:void(0);" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 border-gray-300 rounded-l-md leading-5 hover:text-gray-400 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150 page-link">
                                <span aria-disabled="true">
                                    <span class="relative inline-flex items-center py-2 -ml-px text-sm font-medium text-gray-700 border-gray-300 cursor-default leading-5">{{ $element }}</span>
                                </span>
                            </a>
                        </li>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                        @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                        <li class="page-item">
                            <a href="javascript:void(0)" class="page-active-link page-link" onclick="createPagination({{ $page }})" data-page="{{ $page }}">
                                <span aria-current="page">
                                    <span class="relative inline-flex items-center py-2 -ml-px text-sm font-medium text-gray-500 border-gray-300 cursor-default leading-5">{{ $page }}</span>
                                </span>
                            </a>
                        </li>
                        @else
                        <li class="page-item"> <a href="javascript:void(0)" onclick="createPagination({{ $page }})" data-page="{{ $page }}" class="page-link relative inline-flex items-center py-2 -ml-px text-sm font-medium text-gray-700  border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150 page-link" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                {{ $page }}
                            </a>
                        </li>
                        @endif
                        @endforeach
                        @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($paginator->hasMorePages())
                        <li class="page-item"><a href="javascript:void(0)" onclick="createPagination({{ ( $twtCurrentPage + 1 )  }})" data-page="{{ ( $twtCurrentPage + 1 )}}" rel="next" class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 border border-gray-300 rounded-r-md leading-5 hover:text-gray-400 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150 page-link" aria-label="{{ __('pagination.next') }}">
                                <i class="fa fa-angle-right" aria-hidden="true"></i>
                            </a></li>
                        @else
                        <li class="page-item"><a class="page-link">
                                <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                                    <span class="relative inline-flex items-center py-2 -ml-px text-sm font-medium text-gray-500  border-gray-300 cursor-default rounded-r-md leading-5" aria-hidden="true">
                                        <i class="fa fa-angle-right" aria-hidden="true"></i>
                                    </span>
                                </span>
                            </a></li>
                        @endif
                    </span>
                </ul>
            </div>
        </div>
    </nav>
</div>
@endif