<?php
/*
 * Authorize.Net AIM and ARB Payment Integration
 * Original Author : Peter Indiola
 * Version: $Id: integration.shoppingcart.authorize-arb.php 2479 2014-12-06 03:29:04Z mike $
 */

$__index__ = 'authorizenet_arb';
$__sc_options__[$__index__] = 'Authorize.Net - Automatic Recurring Billing';
//$__sc_affiliates__[$__index__] = '#';
$__sc_videotutorial__[$__index__] = wlm_video_tutorial ( 'integration', 'sc', $__index__ );

if (wlm_arrval($_GET,'cart') == $__index__) {
  if (!$__INTERFACE__) {
    // BEGIN Initialization

    $anetarbthankyou = $this->GetOption('anetarbthankyou');
    if (!$anetarbthankyou) {
      $this->SaveOption('anetarbthankyou', $anetarbthankyou = $this->MakeRegURL());
    }

    // save POST URL
    if (wlm_arrval($_POST, 'anetarbthankyou')) {
      $_POST['anetarbthankyou'] = trim(wlm_arrval($_POST, 'anetarbthankyou'));
      $wpmx = trim(preg_replace('/[^A-Za-z0-9]/', '', $_POST['anetarbthankyou']));
      if ($wpmx == $_POST['anetarbthankyou']) {
        if ($this->RegURLExists($wpmx, null, 'anetarbthankyou')) {
          echo "<div class='error fade'>" . __('<p><b>Error:</b> authorize.net arb Thank You URL (' . $wpmx . ') is already in use by a Membership Level or another Shopping Cart.  Please try a different one.</p>', 'wishlist-member') . "</div>";
        } else {
          $this->SaveOption('anetarbthankyou', $anetarbthankyou = $wpmx);
          echo "<div class='updated fade'>" . __('<p>Thank You URL Changed.&nbsp; Make sure to update authorize.net arb with the same Thank You URL to make it work.</p>', 'wishlist-member') . "</div>";
        }
      } else {
        echo "<div class='error fade'>" . __('<p><b>Error:</b> Thank You URL may only contain letters and numbers.</p>', 'wishlist-member') . "</div>";
      }
    }

    if (isset($_POST['anetarbsettings'])) {
      $anetarbsettings = $_POST['anetarbsettings'];
      $this->SaveOption('anetarbsettings', $anetarbsettings);
    }

    $anetarbthankyou_url = $wpm_scregister . $anetarbthankyou;
    $anetarbsettings = $this->GetOption('anetarbsettings');
    // END Initialization

  } else {
    // START Interface
    $xposts = $this->GetPayPerPosts(array('post_title', 'post_type'));
    $post_types = get_post_types('', 'objects');

    $level_names = array();
    foreach($wpm_levels as $sku => $level) {
      $level_names[$sku] = $level['name'];
    }

    foreach ($xposts AS $post_type => $posts) {
      foreach ((array) $posts AS $post) {
        $level_names['payperpost-' . $post->ID] = $post->post_title;
      }
    }

    $currencies = array('USD', 'AUD','BRL','CAD','CZK','DKK','EUR','HKD','HUF','ILS','JPY','MYR','MXN','NOK','NZD','PHP','PLN','GBP','RUB','SGD','SEK','CHF','TWD','THB','TRY');

    ?>
    <style type="text/css">
    .col-edit { display: none;}
    </style>
    <form method="post" id="stripe_form">
      <h2 class="wlm-integration-steps"><?php _e('Step 1. Setup your API Credentials','wishlist-member'); ?></h2>
      <p><?php _e('You will find your API credentials in your Authorize.net Merchant Interface under<br><strong>Account &raquo; Settings &raquo; Security Settings &raquo; API Login ID and Transaction Key','wishlist-member'); ?></strong></p>
      <table class="form-table">
        <tr>
          <th><?php _e('API Login ID','wishlist-member'); ?></th>
          <td><input type="text" style="width: 300px" name="anetarbsettings[api_login_id]" value="<?php echo $anetarbsettings['api_login_id'] ?>"><br/></td>
        </tr>
        <tr>
          <th><?php _e('Transaction Key','wishlist-member'); ?></th>
          <td><input type="text" style="width: 300px" name="anetarbsettings[api_transaction_key]" value="<?php echo $anetarbsettings['api_transaction_key']  ?>"><br/></td>
        </tr>
        <tr>
          <th><?php _e('Sandbox Mode','wishlist-member'); ?></th>
          <td>
            <label><input type="checkbox" class="sandbox_mode" name="anetarbsettings[sandbox_mode]" value="1" <?php if($anetarbsettings['sandbox_mode'] == 1) echo "checked='checked'"?>><?php _e('Enable','wishlist-member'); ?></label>
          </td>
        </tr>
      </table>

      <input type="submit" name="submit" value="Update API Credentials" class="button-secondary"/>
      </form>

      <h2 class="wlm-integration-steps"><?php _e('Step 2. Configure your Silent Post URL','wishlist-member'); ?></h2>
      <p><?php _e('Copy and paste the URL below in your Authorize.net Merchant Interface under<br><strong>Account &raquo; Settings &raquo; Transaction Format Settings &raquo; Silent Post URL</strong>','wishlist-member'); ?></p>
      <p>&nbsp;<a href="<?php echo $anetarbthankyou_url ?>?action=silent-post" onclick="return false"><?php echo $anetarbthankyou_url ?>?action=silent-post</a></p>

      <h2 class="wlm-integration-steps"><?php _e('Step 3. Manage your Subsciptions','wishlist-member'); ?></h2>
      <br>
      <p class="product-list-loading"><em>Loading subscriptions. Please wait...</em></p>
      <p class="product-list-nothing" style="display:none"><em><?php _e('You have no subscriptions. Create one below.','wishlist-member'); ?></em></p>
      <table class="widefat product-list" style="display:none">
        <thead>
          <tr>
            <th scope="col" width="300"><?php _e('Name', 'wishlist-member'); ?></th>
            <th scope="col"><?php _e('Recurring', 'wishlist-member'); ?></th>
            <th scope="col"><?php _e('Currency', 'wishlist-member'); ?></th>
            <th scope="col"><?php _e('Amount', 'wishlist-member'); ?></th>
            <th scope="col"><?php _e('Membership Level', 'wishlist-member'); ?></th>
          </tr>
        </thead>

        <tbody>
        </tbody>
      </table>

      <div class="add-subscription" style="display:none">
        <p><?php _e('Select a Membership Level or Pay Per Post then click "New Subscription" to create a new subscription','wishlist-member'); ?></p>
        <p style="float:left">
          <select name="sku" class="new-product-level">
            <optgroup label="Membership Levels">
              <?php foreach($wpm_levels as $sku => $l): ?>
              <option value="<?php echo $sku?>"><?php echo $l['name']?></option>
              <?php endforeach; ?>
            </optgroup>

            <?php foreach ($xposts AS $post_type => $posts) : ?>
            <optgroup label="<?php echo $post_types[$post_type]->labels->name; ?>">
              <?php foreach ((array) $posts AS $post): ?>
              <option value="payperpost-<?php echo $post->ID?>"><?php echo $post->post_title?></option>
              <?php endforeach; ?>
            </optgroup>
            <?php endforeach; ?>
          </select>
          <a href="<?php echo $anetarbthankyou_url?>?action=new-subscription" class="button-secondary new-subscription">New Subscription</a>
          <span class="new-subscription-spinner spinner"></span>
        </p>
      </div>

      <script type="text/template" id='product-row'>
        <tr id="product-<%=obj.id%>" class="product-row">
          <td class="column-title col-info col-name">
            <strong><a class="row-title"><%= obj.name %></a></strong>
            <div class="row-actions">
              <span class="edit"><a href="#" rel="<%=obj.id%>" class="edit-product">Edit</a> | </span>
              <span class="delete"><a href="#" rel="<%=obj.id%>" class="delete-product">Delete</a></span>
            </div>
          </td>
          <td class="col-info col-recurring"><% if(obj.recurring == 1) print("YES"); else print ("NO"); %></td>
          <td class="col-info col-currency"><%=obj.currency%></td>
          <td class="col-info col-amount"><%=obj.amount%></td>
          <td class="col-info col-sku">
            <%= obj.name %>
          </td>


          <td class="col-edit col-name">
            <input class="form-val"  size="40" type="text" name="name" value="<%= obj.name %>"/>
          </td>
          <td class="col-edit col-recurring">
            <input type="checkbox" class="form-val"  name="recurring" value="1" <% if(obj.recurring == 1) print('checked=checked') %>/>
          </td>
          <td class="col-edit col-currency">
            <select class="form-val" name="currency">
              <?php foreach($currencies as $c): ?>
                <option <% if(obj.currency == '<?php echo $c?>') print ('selected="selected"') %> name="<?php echo $c?>"><?php echo $c?></option>
              <?php endforeach; ?>
            </select>
          </td>
          <td class="col-edit col-amount">
            <div class="recurring">
              <table>
                <tr>
                  <td>Initial Amount:</td>
                  <td><input class="form-val" type="text" name="init_amount" value="<%=obj.init_amount%>"/> <br/></td>
                </tr>
                <tr>
                  <td>Recurring Amount:</td>
                  <td><input class="form-val" type="text" name="recur_amount" value="<%=obj.recur_amount%>"/> <br/></td>
                </tr>
                <tr>
                  <td>Billing Cycle</td>
                  <td>
                    <select class="form-val" name="recur_billing_frequency">
                    <?php for($i=0; $i<30; $i++): ?>
                      <option <% if(obj.recur_billing_frequency == '<?php echo $i+1?>') print ('selected="selected"') %> value="<?php echo $i+1?>"><?php echo $i+1?></option>
                    <?php endfor; ?>
                    </select>

                    <select class="form-val" name="recur_billing_period">
                      <option <% if(obj.recur_billing_period == 'Day') print ('selected="selected"') %> value="Day">Day</option>
                      <option <% if(obj.recur_billing_period == 'Week') print ('selected="selected"') %> value="Week">Week</option>
                      <option <% if(obj.recur_billing_period == 'Month') print ('selected="selected"') %> value="Month">Month</option>
                      <option <% if(obj.recur_billing_period == 'Year') print ('selected="selected"') %> value="Year">Year</option>
                    </select>
                  </td>
                </tr>


              </table>
            </div>
            <div class="onetime">
              <input class="form-val" type="text" name="amount" value="<%=obj.amount%>"/>
            </div>

          </td>
          <td class="col-edit col-sku">
            <select name="sku" class="form-val">
              <optgroup label="Membership Levels">
                <?php foreach($wpm_levels as $sku => $l): ?>
                <option <% if(obj.sku == '<?php echo $sku?>') print('selected="selected"')%> value="<?php echo $sku?>"><?php echo $l['name']?></option>
                <?php endforeach; ?>
              </optgroup>

              <?php foreach ($xposts AS $post_type => $posts) : ?>
              <optgroup label="<?php echo $post_types[$post_type]->labels->name; ?>">
                <?php foreach ((array) $posts AS $post): ?>
                <option <% if(obj.sku == 'payperpost-<?php echo $post->ID?>') print('selected="selected"')%> value="payperpost-<?php echo $post->ID?>"><?php echo $post->post_title?></option>
                <?php endforeach; ?>
              </optgroup>
              <?php endforeach; ?>
            </select>

            <hr/>
            <p class="form-actions">
              <input class="form-val" type="hidden" name="id" value="<%=obj.id%>"/>
              <button class="button-primary save-product">Save Product</button>
              <button class="button-secondary cancel-edit">Cancel</button>
              <span class="spinner"></span></div>
            </p>
          </td>

        </tr>
      </script>


    <script type="text/javascript">
        var level_names = JSON.parse('<?php echo json_encode($level_names)?>');

        jQuery(function($) {
          $('.dropmenu').on('click', function(ev) {
            ev.preventDefault();
            $('li.dropme ul').not( $(this).parent()).hide();
            console.log($(this).parent().find('ul'));
          });

          function update_fields(el, tr) {
            if (el.val() == 1) {
              tr.find('.amount').find('input').attr('disabled', true).val('');
              tr.find('.plans').find('select').removeAttr('disabled');
            } else {
              tr.find('.plans').find('select').attr('disabled', true).val('');
              tr.find('.amount').find('input').removeAttr('disabled');
            }
          }

          /** table handler **/

          var table_handler = {};


          table_handler.toggle_recurring = function(id) {
            var row = $('#product-' + id);
            var el = row.find('input[name=recurring]');
            if(el.prop('checked')) {
              row.find('.recurring').show();
              row.find('.onetime').hide();
            } else {
              row.find('.recurring').hide();
              row.find('.onetime').show();
            }
          }
          table_handler.remove_row = function(id) {
            $('#product-' + id).remove();
            self.table.find('tr').each(function(i, e) {
              $(e).removeClass('alternate');
              if(i % 2 == 0) {
                $(e).addClass('alternate');
              }
            });
            table_handler.hide_show();
          }

          table_handler.render_row = function(obj) {
            var cnt      = self.table.find('tr').length;
            var template = $("#product-row").html();
            var str      = _.template(template, {'obj': obj} );
            var el       = $('#product-' + obj.id);


            if(el.length > 0) {
              el.replaceWith(str);
            } else {
              self.table.find('tbody').eq(0).append(str);
            }

            table_handler.toggle_recurring(obj.id);


            self.table.find('tr.product-row').removeClass('alternate');
            self.table.find('tr.product-row:even').addClass('alternate');

            table_handler.hide_show();

          }

          table_handler.hide_show = function() {
            $('.product-list-loading').hide();
            $('.add-subscription').show();
            if(self.table.find('tbody tr').length) {
              self.table.show();
              $('.product-list-nothing').hide();
            }else{
              self.table.hide();
              $('.product-list-nothing').show();
            } 
          }
          table_handler.end_edit = function(id) {
            $('#product-' + id).find('td.col-info').show();
            $('#product-' + id).find('td.col-edit').hide();
          }
          table_handler.edit_row = function(id) {
            $('#product-' + id).find('td.col-info').hide();
            $('#product-' + id).find('td.col-edit').show();
          }
          table_handler.fetch = function() {
            $.post(ajaxurl + '?action=wlm_anetarb_all-subscriptions', {}, function(res) {
              var obj = JSON.parse(res);
              if(obj === false || obj.length <= 0) {
                  table_handler.hide_show();
              } else {
                for(i in obj) {
                  table_handler.render_row(obj[i]);
                }
              }
            });
          }
          table_handler.edit_product = function(id) {
            table_handler.edit_row(id);
          }
          table_handler.delete_subscription = function(id) {
            $.post(ajaxurl + '?action=wlm_anetarb_delete-subscription', {id: id}, function(res) {
              table_handler.remove_row(id);
            });
          }
          table_handler.save_subscription = function(id) {
            var row = $('#product-' + id);
            row.find('.spinner').show();

            var data = {};
            row.find('.form-val').each(function(i, e) {
              var el = $(e);
              data[el.prop('name')] = $(el).is(':checkbox')?  ( $(el).is(':checked')? 1 : 0 )  : el.val();
            });


            $.post(ajaxurl + '?action=wlm_anetarb_save-subscription', data, function(res) {
              row.find('.spinner').hide();
              table_handler.render_row(JSON.parse(res));
              table_handler.end_edit(id);
            });


          }
          table_handler.new_subscription = function() {
            $('.new-subscription').attr('disabled','disabled');
            $('.new-subscription-spinner').show();
            var data = {
              'name' : $('.new-product-level option:selected').html(),
              'sku'  : $('.new-product-level').val()
            };
            $.post(ajaxurl + '?action=wlm_anetarb_new-subscription', data, function(res) {
              var obj = JSON.parse(res);
              var template = $("#product-row").html();
              table_handler.render_row(obj);
              $('.new-subscription').removeAttr('disabled');
              $('.new-subscription-spinner').hide();
            });
          }
          table_handler.init = function(table) {
            self.table = table;

            $('.new-subscription').on('click', function(ev) {
              ev.preventDefault();
              table_handler.new_subscription();
            });

            $('.product-list').on('click', '.delete-product', function(ev) {
              ev.preventDefault();
              table_handler.delete_subscription( $(this).attr('rel'));
            });

            $('.product-list').on('click', '.edit-product', function(ev) {
              ev.preventDefault();
              table_handler.edit_product( $(this).attr('rel'));
            });

            $('.product-list').on('click', '.save-product', function(ev) {
              ev.preventDefault();
              var id = $(this).parent().find('input[name=id]').val();
              table_handler.save_subscription(id);
            });

            $('.product-list').on('click', '.cancel-edit', function(ev) {
              ev.preventDefault();
              var id = $(this).parent().find('input[name=id]').val();
              table_handler.end_edit(id);
            });

            $('.product-list').on('change', '.col-recurring input', function(ev) {
              ev.preventDefault();
              var id = $(this).parent().parent().find('input[name=id]').val();
              table_handler.toggle_recurring(id);
            });

            table_handler.fetch();


          }



          table_handler.init($('.product-list'));
          /* end table handler **/
        });
    </script>
    <?php
    include_once($this->pluginDir . '/admin/tooltips/integration.shoppingcart.authorize-arb.tooltips.php');
    // END Interface
  }
}
