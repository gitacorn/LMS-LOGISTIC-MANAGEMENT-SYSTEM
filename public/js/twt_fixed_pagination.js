function createPagination(page_no){
	var site_url = "{{ url('/') }}" + '/';
	searchFields = searchField();
	//searchFields._token = "{{ csrf_token() }}";
	searchFields._token = $.trim($('meta[name="csrf-token"]').attr('content'));
	searchFields.page = page_no;
	$.ajax({
        type: "POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: paginationUrl,
        data: searchFields,
        beforeSend: function() {
            //block ui
            //showLoader();
        },
        success: function(response) {
            $(".ajax-view").html(response);
        }
	});
	
}

/*$(document).ready(function(){
    $('.pagination li a').click(function(){
      $('li a').removeClass("page-active-link");
      $(this).addClass("page-active-link");
  });
});*/