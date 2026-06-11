@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color add-user-section">
    <div class="breadcrumb-wrapper d-md-flex border-navabr">
        <h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }}</h1>
        <nav aria-label="breadcrumb" class="d-flex ml-auto mr-3">
            <ol class="breadcrumb bg-transparent p-0 mb-0 align-self-end">
                <li class="breadcrumb-item"><a href="{{ config('constants.USERS_URL') }}" class="category-add-link">{{ trans("messages.all-employee") }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
            </ol>
        </nav>
    </div>
    <section class="inner-wrapper-common-section dropdown-main main-listing-section user-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="body-form-info reset-bdy-info mt-0 pb-0">
                        {{ Wild_tiger::readMessage() }}
                        {!! Form::open(array( 'id '=> 'add-user-form' , 'method' => 'post' , 'url' => 'users/add')) !!}
                        @if (count($errors) > 0)
                        <div class="error">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <div class="row">
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label for="name" class="control-label">{{ trans("messages.name") }} <span class="star">*</span></label>
                                    <input id="name" class="form-control" type="text" name="name" placeholder="{{ trans('messages.name') }}" value="{{old('name',  ( (isset($recordInfo) && (!empty($recordInfo->v_name))) ?  $recordInfo->v_name : '' ) )}}">
                                    {{ $errors->first('name') }}
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label for="email" class="control-label">{{ trans("messages.email") }} <span class="star">*</span></label>
                                    <input id="email" class="form-control" type="text" name="email" placeholder="{{ trans("messages.email") }}" value="{{old('email',  ( (isset($recordInfo) && (!empty($recordInfo->v_email))) ?  $recordInfo->v_email : '' ) )}}">
                                    {{ $errors->first('email') }}
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label for="mobile" class="control-label">{{ trans("messages.mobile") }} </label>
                                    <input id="mobile" onkeyup="onlyNumberWithSpaceAndPlusSign(this);" maxlength="15" class="form-control" type="text" name="mobile" placeholder="{{ trans('messages.mobile') }}" value="{{old('mobile', ( (isset($recordInfo) && (!empty($recordInfo->v_mobile))) ?  $recordInfo->v_mobile : '' ) )}}">
                                    {{ $errors->first('mobile') }}
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label for="department" class="control-label">{{ trans("messages.department") }}</label>
                                    <input id="department" class="form-control" type="text" name="department" placeholder="{{ trans('messages.department') }}" value="{{old('department', ( (isset($recordInfo) && (!empty($recordInfo->v_department))) ?  $recordInfo->v_department : '' ) )}}">
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label for="password" class="control-label">{{ trans("messages.password") }} <?php echo ((isset($recordInfo) && ($recordInfo->i_id > 0)) ? '' : '') ?></label>
                                    <input id="password" class="form-control" type="password" name="password" placeholder="{{ trans('messages.password') }}" value="">
                                    {{ $errors->first('password') }}
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label for="confirm_password" class="control-label">{{ trans("messages.confirm-password") }} <?php echo ((isset($recordInfo) && ($recordInfo->i_id > 0)) ? '' : '') ?></label>
                                    <input id="confirm_password" class="form-control" type="password" name="confirm_password" placeholder="{{ trans('messages.confirm-password') }}" value="" autocomplete="new-password">
                                    {{ $errors->first('confirm_password') }}
                                </div>
                            </div>


							<?php 
								if(!empty($recordInfo->v_record_type)){
                                	$recordTypeRole = explode(',', $recordInfo->v_record_type);
                                	
								}
                                ?>
                             <?php if( isset($recordInfo) &&  ($recordInfo->v_role ==  config('constants.ROLE_ADMIN') ) ){ ?>
                             
                             <?php } else { ?>   
                            <div class="col-lg-5 col-md-6">
                                <div class="form-group mb-0">
                                    <label for="role" class="control-label">{{ trans("messages.role") }}<span class="star">*</span></label>
                                    <div class="radio-boxes form-row bg-white">
                                        <div class="radio-box col-lg-4 col-6 mb-2 mb-md-0">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="role[]" id="logistic" value="{{ config('constants.LOGISTIC')}}" <?php echo (!isset($recordTypeRole) ? 'checked' : '' ) ?><?php echo ((!empty($recordTypeRole)) && in_array(config('constants.LOGISTIC'), $recordTypeRole)) ? "checked":"" ?>>
                                                <label class="form-check-label custom-type-label btn stock-btn" for="logistic">{{ trans('messages.logistic') }}</label>
                                            </div>
                                        </div>
                                        <div class="radio-box col-lg-4 col-6 mb-2 mb-md-0">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="role[]" id="buyer" value="{{ config('constants.BUYER')}}" <?php echo ((!empty($recordTypeRole)) && in_array(config('constants.BUYER'), $recordTypeRole)) ? "checked":"" ?>>
                                                <label class="form-check-label custom-type-label btn stock-btn" for="buyer">{{ trans('messages.buyer') }}</label>
                                            </div>
                                        </div>
                                        <div class="radio-box col-lg-4 col-sm-6 col-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="role[]" id="goods_in_warehouse" value="{{ config('constants.GOODS_IN_WAREHOUSE')}}" <?php echo ((!empty($recordTypeRole)) && in_array(config('constants.GOODS_IN_WAREHOUSE'), $recordTypeRole)) ? "checked":"" ?>>
                                                <label class="form-check-label custom-type-label btn stock-btn" for="goods_in_warehouse">{{ trans('messages.goods-in-warehouse') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <label id="role[]-error" class="invalid-input" for="role[]"></label>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
								<div class="form-group">
									<label for="warehouse_name" class="control-label">{{ trans('messages.warehouse') }}</label>
									<select class="form-control" name="warehouse_name">
										<option value="">{{ trans("messages.select") }}</option>
										@if(!empty($warehouseDetails))
											@foreach ($warehouseDetails as $warehouseDetail)
												{{ $encodeId = Wild_tiger::encode($warehouseDetail->i_id);}}
												{{ $selected = ''; }}
												@if( isset($recordInfo->i_warehouse_id) && ( $recordInfo->i_warehouse_id == $warehouseDetail->i_id) )
                                        			{{ $selected = "selected='selected'"; }}
                                        		@endif
												<option value="{{ $encodeId }}" {{ $selected }}>{{ (!empty($warehouseDetail->v_warehouse_name) ? $warehouseDetail->v_warehouse_name .(!empty($warehouseDetail->v_warehouse_code) ? ' (' .$warehouseDetail->v_warehouse_code .')' : '' ): '' ) }}</option>
                                        	@endforeach
	                                 	@endif
									</select>
								</div>
							</div>
                            <?php } ?>
                        </div>
                        <?php if(session()->get('role') ==  config('constants.ROLE_ADMIN') ){ ?>
                        <div class="row">
		                <div class="col-md-12 mb-3">
		                    <label for="input"><b><?php echo trans('messages.permission') ?>:</b></label>
		                    <div class="row">
			                    <div class="col-lg-12">
			                        <div class="table-responsive">
			                            <table class="table table-sms table-bordered  table-hover bg-white">
			                                <thead class="text-center">    
			                                	<tr>
			                                    	<th><?php echo trans('messages.module')?></th>                                                
			                                    	<th><?php echo trans('messages.view')?></th>
			                                    	<th><?php echo trans('messages.add')?></th>
			                                    	<th><?php echo trans('messages.edit')?></th>
			                                    	<th><?php echo trans('messages.delete')?></th>
			                                    	<th>{{ trans('messages.export-excel') }}</th>
			                                    </tr>
			                                </thead>
		                                    
			                                <tbody>
			                                   	<?php 
			                                       	$defaultPermission = ( ( isset($recordInfo) && ( !empty( $recordInfo->v_permission ) ) ) ? explode( ",", $recordInfo->v_permission ) : [] );;
													if(!empty($allPermission)){
			                                        	foreach($allPermission as $premission){
			                                        		?>
			                                        		<tr class="border-top permission-row">
			                                        			<th>
			                                        				<div class="mb-3">
																		<div class="custom-control custom-checkbox">
																			<input type="checkbox" class="custom-control-input parent-checkbox"  id="pmsn_<?php echo $premission->i_id ;?>">
																			<label class="custom-control-label" for="pmsn_<?php echo $premission->i_id ;?>"><?php echo $premission->v_group_name ?></label>
																		</div>
																	</div>
			                                        			</th>
			                                        			<?php
			                                        				$permissionIds = explode("," , $premission->permission_ids);
			                                        				$permissionTitles = explode("," , $premission->permissionTitle);
			                                        			
			                                        				if((!empty($permissionIds)) && (!empty($permissionTitles))){
			                                        					foreach($permissionIds as $key => $permissionId){
				                                        					$checked = "";
				                                        					if(in_array($permissionId,$defaultPermission)){
				                                        						$checked = "checked='checked'";
				                                        					}
			                                        						?>
				                                        					<td>
					                                        					<div class="mb-3">
																					<div class="custom-control custom-checkbox">
																						<input type="checkbox" class="custom-control-input child-checkbox"  value="<?php echo $permissionId;?>" name="permission[]" id="permission_<?php echo $permissionId ;?>" <?php echo $checked?>>
																						<label class="custom-control-label" for="permission_<?php echo $permissionId ;?>"><?php echo ucwords( $permissionTitles[$key] );?></label>
																					</div>
																				</div>
																			</td>
			                                        						<?php
			                                        					}
			                                        				}
			                                        			?>
			                                        		</tr>
			                                        		<?php
			                                        	}
			                                    	}
			                                	?>
				                        	</tbody>
		                                    
				                    	</table>
				                	</div>  
			                    </div>
							</div>
			        	</div>
		            </div>
		            <?php } ?>
		            <div class="row submit-sticky">
                        <div class="col-md-12 ">
                            <?php if (isset($recordInfo) && ($recordInfo->i_id > 0)) { ?>
                                <input type="hidden" name="record_id" value="{{ Wild_tiger::encode($recordInfo->i_id)}}">
                                <button type="submit" title="{{ trans('messages.update') }}" class="btn btn bg-theme text-white btn-wide">{{ trans("messages.update") }}</button>
                            <?php } else { ?>
                                <button type="submit" title="{{ trans('messages.submit') }}" class="btn btn bg-theme text-white btn-wide">{{ trans("messages.submit") }}</button>
                            <?php } ?>
                            <a href="{{ config('constants.USERS_URL') }}" title="{{ trans('messages.cancel') }}" class="btn btn-outline-secondary shadow-sm btn-wide">{{ trans("messages.cancel") }}</a>
                        </div>
                        {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<script>
    $("#add-user-form").validate({
        errorClass: "invalid-input",
        rules: {
            name: {
                required: true,
                noSpace: true
            },
            password: {
                required: function(element) {
                    return ((($.trim($("[name='record_id']").val()) != null) && ($.trim($("[name='record_id']").val()) != "")) ? false : false)
                },
                noSpace: true
            },
            confirm_password: {
                required: function(element) {
                    return ((($.trim($("[name='record_id']").val()) != null) && ($.trim($("[name='record_id']").val()) != "")) ? false : false)
                },
                noSpace: true,
                equalTo: "#password"
            },
            email: {
                required: true,
                noSpace: true,
                email_regex: true,
                validateUniqueEmail: true
            },
            mobile: {
                required: false,
                noSpace: true,
                //mobile_regex: true
            },
            'role[]': {
                required: true,
                noSpace: true
            },
        },
        messages: {
            name: {
                required: '{{ trans("messages.required-name") }}'
            },
            password: {
                required: '{{ trans("messages.required-password") }}'
            },
            confirm_password: {
                required: '{{ trans("messages.required-confirm-password") }}',
                equalTo: '{{ trans("messages.confirm-password-not-match") }}'
            },
            email: {
                required: '{{ trans("messages.required-login-email") }}'
            },
            mobile: {
                required: '{{ trans("messages.required-enter-mobile") }}'
            },
            'role[]': {
                required: '{{ trans("messages.required-role") }}'
            },
        },
        submitHandler: function(form) {
            showLoader();
            form.submit();
        },
    });
    $(function(){
    	$('.parent-checkbox').on('click',function(){
    		if($(this).prop('checked') != false ){
    			$(this).parents('.permission-row').find('.child-checkbox').prop('checked',true);
    		} else if($(this).prop('checked') != true)  {
    			$(this).parents('.permission-row').find('.child-checkbox').prop('checked',false);	
    		}
    	})

    })
</script>
@endsection