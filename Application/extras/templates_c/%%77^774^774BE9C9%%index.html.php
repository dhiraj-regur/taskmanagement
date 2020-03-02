<?php /* Smarty version 2.6.19, created on 2020-03-02 12:04:56
         compiled from index.html */ ?>
<link href="/front/css/style.css" rel="stylesheet">
<div id="partner_login">
 <article class="sec_lg">
        <h1 class="align_center"> Login</h1>
        <div class="align_center">
       
         
		 
          <form id="login"  action="" method="post">
						 <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../../../views/partials/error_list_front.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php if ($this->_tpl_vars['error_message']): ?>
					
						<div class="error"><?php echo $this->_tpl_vars['error_message']; ?>
</div>
					
			<?php endif; ?>
            <div class="form-group has-icon has-feedback ">
            <input type="hidden" name="retURL" value="<?php echo $this->_tpl_vars['retURL']; ?>
"/>
              <input class="form-control" name="email" id="email" type="email" value="<?php echo $this->_tpl_vars['email']; ?>
" placeholder="Your email address"/>
              <span class="icon glyphicon glyphicon-user"></span>
              <span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
              <span class="glyphicon glyphicon-remove"></span>
            </div>

            <div class="form-group has-icon">
              <input class="form-control" name="password" id="password" type="password" value=""  placeholder="Your password"/>
              <span class="icon glyphicon glyphicon-lock"></span>
              <span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
              <span class="glyphicon glyphicon-remove"></span>
            </div>

            <div class="row">
              <!--<div class="forgot col-sm-6">
                <a href="/taskmanagement/resetpassword" title="Forgot Password?">Forgot Password?</a>
              </div>-->
              <div class="login_btn col-sm-6">
                <button type="submit" class="btn btn-primary" value="Login">Login</button>
              </div>
						</div>
						

          </form>

        </div>
      </article>

      <aside id="no_account">
        <div class="container">
          <p>Don't have an account? <a href="/register/" title="Create Account">Create one</a>
        </div>
      </aside>
</div>

<?php ob_start(); ?>

	<?php echo '
	<script language="javascript">

		jQuery("form#login").validate({
		    rules: {    	
				email: {
		           required: true,
		           email: true
		       },      
		       password: "required"
		    },
		    messages: {
				
		        email:{
		        	required: "Email is required",
		            email:"Please enter a valid email address: (e.g. test@pinlocal.com)"
		            
		            },
		        
		        password: "Password is required"
		       
		    } ,
		    errorElement: "span",
		    errorPlacement: function(error, element) {
		        error.insertAfter(element);
		    },
		    highlight: function(element) {
		    	$(element).parent().removeClass("has-success");
		        $(element).parent().addClass("has-error");
		        $(element).css("border-color", "#843534");
		      
		    },
		    unhighlight: function(element) {
		    	$(element).parent().removeClass("has-error");
		        $(element).parent().addClass("has-success");         
		        $(element).css("border-color", "#00c572");
		        
		    }  
		    
		});
	 </script>

	 '; ?>


	<?php $this->_smarty_vars['capture']['page_js'] = ob_get_contents(); ob_end_clean(); ?>
