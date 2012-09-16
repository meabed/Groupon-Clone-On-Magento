var Onestepcheckout=Class.create();
Onestepcheckout.prototype={
    initialize:function(a){
        this.loadWaitingReview=this.loadWaitingPayment=this.loadWaitingShippingMethod=false;
        this.failureUrl=a.failure;this.reloadReviewUrl=a.reloadReview;
        this.reloadPaymentUrl=a.reloadPayment;
        this.successUrl=a.success;
        this.response=[]
        },
        ajaxFailure:function(){

            location.href=this.failureUrl
        },
        processRespone:function(a){

            var b;
            if(a&&a.responseText)
            try{
                b=a.responseText.evalJSON()

                }
            catch(c){
                b={}
            }

            if(b.redirect){ 
                location.href=b.redirect;}
            else if(b.error){

		        if(b.fields){
		            a=b.fields.split(",");
		            for(var d=0;d<a.length;d++)
		            null==$(a[d])&&Validation.ajaxError(null,b.error)
		        }
		        else{ 
				if(b.error && b.payment){
					this.updatePayment();payment.initWhatIsCvvListeners()
					}
				else{
		    			alert(Translator.translate(b.error_messages));
				}
			}
		}
		else if(b.response_status_detail){
		    alert(Translator.translate(b.response_status_detail));
		}
            else{
                this.response=b;

                if(b.shippingMethod)
		{
                    this.updateShippingMethod();}
                else if(b.payment){

                    this.updatePayment();payment.initWhatIsCvvListeners()
                }
                else{

                this.updateReview()}
            }
        },
        setLoadWaitingShippingMethod:function(a){
            this.loadWaitingShippingMethod=a;
            if(a==true){
                $('quickcheckout-ajax-loader-text').update(ShippingMethods_Loading_Text);
                $("quickcheckout-ajax-loader")&&Element.show("quickcheckout-ajax-loader");
                $("checkout-shipping-method-load")&&Element.hide("checkout-shipping-method-load")
            }
            else{
                if($("billing:use_for_shipping_yes").checked==true){
                    $("quickcheckout-ajax-loader")&&Element.hide("quickcheckout-ajax-loader");
                }
                $("checkout-shipping-method-load")&&Element.show("checkout-shipping-method-load")
            }
          
        },
        resetLoadWaitingShippingMethod:function(){
            this.setLoadWaitingShippingMethod(false)
        },
        updateShippingMethod:function(){
            if($("checkout-shipping-method-load")){

                $("checkout-shipping-method-load").update(this.response.shippingMethod);
                this.resetLoadWaitingShippingMethod();
                if($$("#checkout-shipping-method-load .no-display input").length!=0){
                    $$("#checkout-shipping-method-load .no-display input")[0].checked==true&&shippingMethod.saveShippingMethod();}
                else{
                    this.response.payment&&this.reloadPayment()}
            }
            else{

                this.response.payment&&this.reloadPayment()
	    }
        },
        setLoadWaitingPayment:function(a){
            this.loadWaitingPayment=a;
             if(a==true){
                $('quickcheckout-ajax-loader-text').update(Payment_Loading_Text);
                $("quickcheckout-ajax-loader")&& Element.show("quickcheckout-ajax-loader");
                $("checkout-payment-method-load")&&Element.hide("checkout-payment-method-load")
            }
            else{
                $("checkout-payment-method-load")&&Element.show("checkout-payment-method-load")
            }
        },
        resetLoadWaitingPayment:function(){
            this.setLoadWaitingPayment(false)
        },
        updatePayment:function(){
            $("checkout-payment-method-load").update(this.response.payment);
            this.resetLoadWaitingPayment();
            payment.switchMethod(payment.currentMethod);
            if($$("#checkout-payment-method-load .no-display input").length!=0)
                $$("#checkout-payment-method-load .no-display input")[0].checked==true&&payment.savePayment();
            else{
                
                var a=false;
                $$("#checkout-payment-method-load input").each(function(b){
                    if(b.checked==true)
                       { a=true;
			}
                });
                if(!a){
                     $$("#checkout-payment-method-load input").each(function(b){
                        b.checked=true;
                        a=true;
                        throw $break;
                    });
                }
                a==true?payment.savePayment():this.reloadReview()
            }
        },
        setLoadWaitingReview:function(a){
            this.loadWaitingReview=a;
              if(a==true){
                $('quickcheckout-ajax-loader-text').update(Review_Loading_Text);
                $("quickcheckout-ajax-loader")&&Element.show("quickcheckout-ajax-loader");
                $("checkout-review-load")&&Element.hide("checkout-review-load")
            }
             else if(a=='saving_order'){
               $('quickcheckout-ajax-loader-text').update(Review_Submitting_Text);
                $("quickcheckout-ajax-loader")&&Element.show("quickcheckout-ajax-loader");
            }
             else{
                $("quickcheckout-ajax-loader")&&Element.hide("quickcheckout-ajax-loader");
                $("checkout-review-load")&&Element.show("checkout-review-load")
            }
        },
        resetLoadWaitingReview:function(){
            this.setLoadWaitingReview(false)
        },
        updateReview:function(){
            $("checkout-review-load").update(this.response.review);
            this.resetLoadWaitingReview();
            if(this.response.success)
            location.href=this.successUrl
        },
        reloadReview:function(){
            this.setLoadWaitingReview(true);
            new Ajax.Request(this.reloadReviewUrl,{
                        method:"post",
                        onComplete:this.resetLoadWaitingReview,
                        onSuccess:this.processRespone.bind(this),
                        onFailure:this.ajaxFailure.bind(this)
                    })
        },
        reloadPayment:function(){
            this.setLoadWaitingPayment(true);
            new Ajax.Request(this.reloadPaymentUrl,{
                method:"post",
                onComplete:this.resetLoadWaitingPayment,
                onSuccess:this.processRespone.bind(this),
                onFailure:this.ajaxFailure.bind(this)
            })
        },
        showOptionsList:function(a,b){
            if(a){
                new Effect.toggle(b,"appear");
                new Effect.toggle(a.id,"appear");
                console.log(a.id.substring(0,10));
                if(a.id.substring(0,10)=="option-exp")
                    new Effect.toggle("option-clo-"+a.id.substring(11));
                else
                    new Effect.toggle("option-exp-"+a.id.substring(11))
            }
        }
    };
    
    var Login=Class.create();
    Login.prototype={
        initialize:function(a){
            this.loginUrl=a;
            this.loadWaitingLogin=false;
            this.response=[]
        },
        show:function(){
            $("quickcheckout-login").setStyle({
                opacity:0,
                visibility:"visible"
            });
            new Effect.Opacity("quickcheckout-login",{
                duration:0.9,from:0,to:0.8
            });
            Element.show("quickcheckout-login-form")
        },
        hide:function(){
            new Effect.Opacity("quickcheckout-login",{
                duration:0.9,
                from:0.8,to:0,
                afterFinish:function(){
                    $("quickcheckout-login").setStyle({
                        opacity:0,visibility:"hidden"
                    })
                }
            });
            Element.hide("quickcheckout-login-form")
        },
        login:function(){
            var a=$("login-form"); 
            if((a=new Validation(a))&&a.validate()){
                a=$("login-email").value;
                var b=$("login-password").value;
                this.setLoadWaitingLogin(true);
                new Ajax.Request(this.loginUrl,{
                    parameters:{username:a,password:b},
                    method:"post",
                    onComplete:this.resetLoadWaitingLogin,
                    onSuccess:this.processRespone.bind(this),
                    onFailure:onestepcheckout.ajaxFailure.bind(this)
                    })
                }
        },
        processRespone:function(a){
            var b;
            if(a&&a.responseText)
                try{
                    b=a.responseText.evalJSON()
                }catch(c){
                    b={}
                }
            if(b.error){
                
                $("quickcheckout-error-message").update(b.error);
                this.resetLoadWaitingLogin()
            }else
            location.href=""
        },
        setLoadWaitingLogin:function(a){
            if(this.loadWaitingLogin==a){
                
                Element.hide("inner_loader");
                Element.show("quickcheckout-login-form")
            }else{
                $('inner_loader').update(login_loading_text);
                Element.show("inner_loader");
                Element.show("quickcheckout-login-form")
            }
        },
        resetLoadWaitingLogin:function(){
            this.setLoadWaitingLogin(false)
        }
    };
    
    var Forgotpass=Class.create();
    Forgotpass.prototype={
        initialize:function(a){
            this.forgotpassUrl=a;
            this.response=[]
        },
        forgotpass:function(){
            var a=$("forgotpass-form");
            if((a=new Validation(a))&&a.validate()){
                a=$("email_address").value;
                this.setLoadWaitingforgot(false);
                new Ajax.Request(this.forgotpassUrl,{
                    parameters:{email:a},
                    method:"post",
                    onComplete:this.setLoadWaitingforgot(false),
                    onSuccess:this.processRespone.bind(this),
                    onFailure:onestepcheckout.ajaxFailure.bind(this)
                    })
                }
        },
        processRespone:function(a){
            var b;
            if(a&&a.responseText)
                try{
                    b=a.responseText.evalJSON()
                }catch(c){
                    b={}
                }
            if(b.error){
                $("quickcheckout-forgotpass-error-message").update(b.error);
                this.setLoadWaitingforgot(true)
            }
        },
        setLoadWaitingforgot:function(a){
            if(a){
                
                Element.hide("inner_loader_forgotpass");
            }else{
                $('inner_loader_forgotpass').update(forgot_loading_text);
                Element.show("inner_loader_forgotpass");
            }
        }
    };
    
     
    var Billing=Class.create();
    Billing.prototype={
        initialize:function(a,b,c,d){
            this.useBilling=a;
            this.saveCountryUrl=b;
            this.switchMethodUrl=c;
            this.addressUrl=d
        },
        enalbleShippingAddress:function(){
            this.setStepNumber();
            if($("billing:use_for_shipping_yes").checked==true){
                Element.show("shipping-address-form");
                this.useBilling=false;
                $("shipping-address-select")?shipping.setAddress($("shipping-address-select").value):shipping.saveCountry();
               
            }else{
                Element.hide("shipping-address-form");
                this.useBilling=true;
                this.saveCountry()
            }
        },
        saveCountry:function(){
            var a=$("billing:country_id").value,
            b=$("billing:postcode").value;
	    c=$("billing:region_id").value;
            if(this.useBilling){
                onestepcheckout.setLoadWaitingShippingMethod(true);
                new Ajax.Request(this.saveCountryUrl,{
                    parameters:{country_id:a,postcode:b,region_id:c,use_for:"billing"},
                    method:"post",
                    onComplete:onestepcheckout.resetLoadWaitingShippingMethod.bind(onestepcheckout),
                    onSuccess:onestepcheckout.processRespone.bind(onestepcheckout),
                    onFailure:onestepcheckout.ajaxFailure.bind(onestepcheckout)
                })
                
            }else{
                onestepcheckout.setLoadWaitingPayment(true);
                new Ajax.Request(this.saveCountryUrl,{
                    parameters:{country_id:a,postcode:b,region_id:c,use_for:"shipping"},
                    method:"post",
                    onComplete:onestepcheckout.resetLoadWaitingPayment.bind(onestepcheckout),
                    onSuccess:onestepcheckout.processRespone.bind(onestepcheckout),
                    onFailure:onestepcheckout.ajaxFailure.bind(onestepcheckout)
                })
               
            }
        },
        register:function(){
            var a="";
            if($("billing:register").checked==true&&$("billing:register").value==1){
                Element.show("register-customer-password");
                a="register"
            }else{
                Element.hide("register-customer-password");
                a="guest"
            }
            a&&new Ajax.Request(this.switchMethodUrl,{
                parameters:{method:a},
                method:"post",
                onFailure:onestepcheckout.ajaxFailure.bind(onestepcheckout)
            })
        },
        setAddress:function(a){
            if(a)request=new Ajax.Request(this.addressUrl+a,{
                method:"get",
                onSuccess:this.fillForm.bindAsEventListener(this),
                onFailure:onestepcheckout.ajaxFailure.bind(onestepcheckout)
            })
        },
        newAddress:function(a){
            if(a){
                this.resetSelectedAddress();
                Element.show("billing-new-address-form")
            }else
                Element.hide("billing-new-address-form")
        },
        resetSelectedAddress:function(){
            var a=$("billing-address-select");
            if(a)a.value=""
        },
        fillForm:function(a){
            var b={};
            if(a&&a.responseText)
            try{
                b=a.responseText.evalJSON()
            }
            catch(c){
                b={}
            }else
                this.resetSelectedAddress();
                arrElements=Form.getElements(review.form);
                for(var d in arrElements)
                if(arrElements[d].id){
                    a=arrElements[d].id.replace(/^billing:/,"");
                    if(b[a]!=undefined&&b[a])
                    arrElements[d].value=b[a]
                }
                this.saveCountry()
        },
        setStepNumber:function(){
            steps=$$("#quickcheckout-number");
            for(var a=0;a<steps.length;a++)
                if(steps[a].className!="step-1"&&steps[a].className!="step-review")
                    if($("billing:use_for_shipping_yes").checked==true){
                        steps[a].className!="shipping"&&steps[a].removeClassName("step-"+a);
                        steps[a].addClassName("step-"+(a+1))
                    }else{
                        steps[a].className!="step-2"&&steps[a].addClassName("step-"+a);
                        steps[a].removeClassName("step-"+(a+1))
                    }
        }
    };
    var Shipping=Class.create();
    Shipping.prototype={
        initialize:function(a,b){
            this.saveCountryUrl=a;
            this.addressUrl=b
        },
        saveCountry:function(){
            if(billing.useBilling==false){
                var a=$("shipping:country_id").value,
                b=$("shipping:postcode").value;
		c=$("shipping:region_id").value;
                onestepcheckout.setLoadWaitingShippingMethod(true);
                new Ajax.Request(this.saveCountryUrl,{
                    parameters:{country_id:a,postcode:b,region_id:c},
                    method:"post",
                    onComplete:onestepcheckout.resetLoadWaitingShippingMethod.bind(onestepcheckout),
                    onSuccess:onestepcheckout.processRespone.bind(onestepcheckout),
                    onFailure:onestepcheckout.ajaxFailure.bind(onestepcheckout)
                })
            }
        },
        setAddress:function(a){
            if(a)request=new Ajax.Request(this.addressUrl+a,{
                method:"get",
                onSuccess:this.fillForm.bindAsEventListener(this),
                onFailure:onestepcheckout.ajaxFailure.bind(onestepcheckout)
            })
        },
        newAddress:function(a){
            if(a){
                this.resetSelectedAddress();
                Element.show("shipping-new-address-form")
            }else
            Element.hide("shipping-new-address-form");
            shipping.setSameAsBilling(false)
        },
        resetSelectedAddress:function(){
            var a=$("shipping-address-select");
            if(a)a.value=""
        },
        setSameAsBilling:function(a){
            ($("shipping:same_as_billing").checked=a)&&this.syncWithBilling()
        },
        syncWithBilling:function(){
            $("billing-address-select")&&this.newAddress(!$("billing-address-select").value);
            $("shipping:same_as_billing").checked=true;
            if(!$("billing-address-select")||!$("billing-address-select").value){
                arrElements=Form.getElements(review.form);
                for(var a in arrElements)if(arrElements[a].id){
                    var b=$(arrElements[a].id.replace(/^shipping:/,"billing:"));
                    if(b)
                    arrElements[a].value=b.value
                }
                shippingRegionUpdater.update();
                $("shipping:region_id").value=$("billing:region_id").value;
                $("shipping:region").value=$("billing:region").value
            }else
                $("shipping-address-select").value=$("billing-address-select").value
        },
        fillForm:function(a){
            var b={};
            if(a&&a.responseText)
                try{
                    b=a.responseText.evalJSON()
                }catch(c){
                    b={}
                }
            else
                this.resetSelectedAddress();
                arrElements=Form.getElements(review.form);
                for(var d in arrElements)
                    if(arrElements[d].id){
                        a=arrElements[d].id.replace(/^shipping:/,"");
                        if(b[a]!=undefined&&b[a])arrElements[d].value=b[a]
                    }
                    this.saveCountry()
        },
        setRegionValue:function(){
            $("shipping:region").value=$("billing:region").value
        }
    };
    var ShippingMethod=Class.create();
    ShippingMethod.prototype={initialize:function(a,b){
        this.saveUrl=a;
        this.isReloadPayment=b

    },
    saveShippingMethod:function(){
        for(var a=document.getElementsByName("shipping_method"),b="",c=0;c<a.length;c++)
            if(a[c].checked)b=a[c].value;
            if(b!=""){

                this.isReloadPayment==1&&onestepcheckout.setLoadWaitingPayment(true);
                new Ajax.Request(this.saveUrl,{
                                    parameters:{shipping_method:b},
                                    method:"post",
                                    onComplete:onestepcheckout.resetLoadWaitingPayment.bind(onestepcheckout),
                                    onSuccess:onestepcheckout.processRespone.bind(onestepcheckout),
                                    onFailure:onestepcheckout.ajaxFailure.bind(onestepcheckout)
                })
            }
        }
    };
    var Payment=Class.create();
    Payment.prototype={
       
        beforeInitFunc:$H({}),
        afterInitFunc:$H({}),
        beforeValidateFunc:$H({}),
        afterValidateFunc:$H({}),
        initialize:function(a){
            this.saveUrl=a
            },
            init:function(){
                for(var a=$$("input[name^=payment]"),b=null,c=0;c<a.length;c++)
                { 
                    if(a[c].name=="payment[method]")
                    {
                        if(a[c].checked)b=a[c].value
                    }
                    else
                   
                        a[c].disabled=true;
                        a[c].setAttribute("autocomplete","off")
                }
                b&&this.switchMethod(b)
            },
            savePayment:function(){
                var a=document.getElementsByName("payment[method]");

                value="";
                for(var b=0;b<a.length;b++)
                    if(a[b].checked)value=a[b].value;
                    if(value!=""){onestepcheckout.setLoadWaitingReview(true);
                  
                        new Ajax.Request(this.saveUrl,{
                            parameters:{method:value},
                            method:"post",
                            onComplete:onestepcheckout.resetLoadWaitingReview.bind(onestepcheckout),
                            onSuccess:onestepcheckout.processRespone.bind(onestepcheckout),
                            onFailure:onestepcheckout.ajaxFailure.bind(onestepcheckout)
                        })
                    }
            },
            switchMethod:function(a){
               
                if(this.currentMethod&&$("payment_form_"+this.currentMethod))
                { 
                    var b=$("payment_form_"+this.currentMethod);
                    b.hide();
                    b=b.select("input","select","textarea");
                    for(var c=0;c<b.length;c++)b[c].disabled=true
                }
                if($("payment_form_"+a))
                { 
                    b=$("payment_form_"+a);
                    b.show();
                    b=b.select("input","select","textarea");
                    for(c=0;c<b.length;c++)b[c].disabled=false
                }else
              
                    $(document.body).fire("payment-method:switched",{
                        method_code:a
                    });
                this.currentMethod=a
            },
            initWhatIsCvvListeners:function(){
                $$(".cvv-what-is-this").each(function(a){
                    Event.observe(a,"click",toggleToolTip)
                })
            }
        };
	

	
        var Review=Class.create();
        Review.prototype={
            initialize:function(a,b,c){
                this.form=a;
                this.saveUrl=b;
                this.agreementsForm=c;
                this.onestepcheckourForm=new VarienForm(this.form)
            },
            save:function(){
                if((new Validation(this.form)).validate()){
                   //Element.show("quickcheckout-ajax-loader")
                   onestepcheckout.setLoadWaitingReview('saving_order');
                    var a=Form.serialize(this.form);
                    if(this.agreementsForm)
                    a+="&"+Form.serialize(this.agreementsForm);
		    if($(payment.currentMethod+"_cc_type")){
			   pay="payment%5Bcc_type%5D="+$(payment.currentMethod+"_cc_type").value+"&payment%5Bcc_exp_month%5D="+$(payment.currentMethod+"_expiration").value+"&payment%5Bcc_exp_year%5D="+$(payment.currentMethod+"_expiration_yr").value;
		    a+="&"+pay;
		    }
		  a.save=true;
		    if(payment.currentMethod.startsWith('sage')){
		       
			new Ajax.Request(addaddressb4review,{
			    method:"post",
			    parameters:a,
			    onComplete:onestepcheckout.resetLoadWaitingReview.bind(onestepcheckout),
			    onSuccess:this.processRespone.bind(this),
			    onFailure:onestepcheckout.ajaxFailure.bind(false)
			})
		    }
		    else{
			 
			if(!payment.currentMethod.startsWith('sage')){
			    this.saveUrl=review_url;
			}
			new Ajax.Request(this.saveUrl,{
			    method:"post",
			    parameters:a,
			    onComplete:onestepcheckout.resetLoadWaitingReview.bind(onestepcheckout),
			    onSuccess:onestepcheckout.processRespone.bind(onestepcheckout),
			    onFailure:onestepcheckout.ajaxFailure.bind(onestepcheckout)
			})
		    }
		}
            },
	    processRespone:function(a){
            var b;
            if(a&&a.responseText)
                try{
                    b=a.responseText.evalJSON()
                }catch(c){
                    b={}
                }
		//this.setLoadWaitingcoupon(true);
	    

            if(b.success){
		   if(!payment.currentMethod.startsWith('sage')){
		   
                   this.saveUrl=review_url;
		}
                    new Ajax.Request(this.saveUrl,{
                        method:"post",
                        parameters:a,
                        onComplete:onestepcheckout.resetLoadWaitingReview.bind(onestepcheckout),
                        onSuccess:onestepcheckout.processRespone.bind(onestepcheckout),
                        onFailure:onestepcheckout.ajaxFailure.bind(onestepcheckout)
                    })
		
            }
	    
        }
        };
       var Quickcheckout_Agreements=Class.create();
        Quickcheckout_Agreements.prototype={
           initialize:function(){
                },
            show:function(c){
                a='QC-AG-Bk';
                a+=c;
                b='QC-AG-CO';
                b+=c;
                $(a).setStyle({
                    opacity:0,
                    visibility:"visible"
                });
                new Effect.Opacity(a,{
                    duration:0.9,from:0,to:0.8
                });
                Element.show(b)
            },
            hide:function(c){
                a='QC-AG-Bk'+c;
                b='QC-AG-CO'+c;
                new Effect.Opacity(a,{
                    duration:0.9,
                    from:0.8,to:0,
                    afterFinish:function(){
                        $(a).setStyle({
                            opacity:0,visibility:"hidden"
                        })
                    }
                });
                Element.hide(b)
            }
        };
        
    var Coupon=Class.create();
    Coupon.prototype={
        initialize:function(a){
            this.CouponUrl=a;
	   // this.reload=false;
            this.response=[]
        },
	show:function(){
	    this.reload=false;
            $("quickcheckout-coupon").setStyle({
                opacity:0,
                visibility:"visible"
            });
            new Effect.Opacity("quickcheckout-coupon",{
                duration:0.9,from:0,to:0.8
            });
            Element.show("quickcheckout-coupon-form")
        },
        hide:function(){
            new Effect.Opacity("quickcheckout-coupon",{
                duration:0.9,
                from:0.8,to:0,
                afterFinish:function(){
                    $("quickcheckout-coupon").setStyle({
                        opacity:0,visibility:"hidden"
                    })
                }
            });
            Element.hide("quickcheckout-coupon-form")
	    //if(this.reload==true){
		//onestepcheckout.reloadReview();
	   // }
	    
        },
        coupon:function(remove){
	    var b=0;
	    if(remove){
		$("coupon_code").removeClassName('required-entry');
		b="1";
	    }
	    else{
		$("coupon_code").addClassName('required-entry');
	    }
            var c=$("coupon_form");
	     
           if((c=new Validation(c))&&c.validate()){
                c=$("coupon_code").value;
                this.setLoadWaitingcoupon(false);
                new Ajax.Request(this.CouponUrl,{
                    parameters:{coupon_code:c,remove:b},
                    method:"post",
                    onComplete:this.setLoadWaitingcoupon(false),
                    onSuccess:this.processRespone.bind(this),
                    onFailure:onestepcheckout.ajaxFailure.bind(this)
                    })
              }
        },
        processRespone:function(a){
            var b;
            if(a&&a.responseText)
                try{
                    b=a.responseText.evalJSON()
                }catch(c){
                    b={}
                }
		this.setLoadWaitingcoupon(true);

            if(b.error){
		    Element.hide("remove_coupon")
		
		$("quickcheckout-coupon-message").setStyle({
		    color:"red"
		});
                $("quickcheckout-coupon-message").update(b.error);
		if(b.reload==true)
		billing.saveCountry();
            }
	    else if(b.success){
		 Element.show("remove_coupon")
		$("quickcheckout-coupon-message").setStyle({
		    color:"green"
		});
                $("quickcheckout-coupon-message").update(b.success);
		if(b.reload==true)
		billing.saveCountry();
            }
        },
        setLoadWaitingcoupon:function(a){
            if(a){
                Element.hide("inner_loader_coupon");
            }else{
              
                Element.show("inner_loader_coupon");
            }
        }
    };
        
     var CartUpdate=Class.create();
    CartUpdate.prototype={
        initialize:function(a){
	   
            this.CartUrl=a;
	   // this.reload=false;
            this.response=[]
        },
	
        cartupdate:function(id,action){
	   
              
               // this.setLoadWaitingcoupon(false);
                new Ajax.Request(this.CartUrl,{
                    parameters:{productid:id,update:action},
                    method:"post",
                    onComplete:onestepcheckout.setLoadWaitingShippingMethod(false),
                    onSuccess:this.processRespone.bind(this),
                    onFailure:onestepcheckout.ajaxFailure.bind(this)
                    })
              
        },
        processRespone:function(a){
            var b;
            if(a&&a.responseText)
                try{
                    b=a.responseText.evalJSON()
                }catch(c){
                    b={}
                }
		//onestepcheckout.setLoadWaitingShippingMethod(false);
	     if(b.error){
		   alert(b.error);
		
            }
	    else if (b.success){
		 
		billing.saveCountry();
            }
        }
    };
        

