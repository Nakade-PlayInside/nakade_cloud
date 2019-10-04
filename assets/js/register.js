// assets/js/imprint
// custom code for the imprint page
import '../css/register.scss';

/* Toggles the password field readability of the plain password.
  @license http://www.opensource.org/licenses/mit-license.html  MIT License
  @copyright   Copyright (C) - 2019 Dr. Holger Maerz
  @author Dr. H.Maerz <holger@nakade.de>

REQUIREMENTS:

 - Font Awesome (fa-eye, fa-eye-slash)
 - jQuery
 - Twitter bootstrap (for css)

 Add class toggle-eye to your eye element. This will trigger the password readability.
 Use the bootstrap classes input-group, input-group-append, input-group-text for correct styling (see Bootstrap
 documentation).
 Add class toggle-password to your input element.

 EXAMPLE:

 for append eye field

  <div class="input-group mb-2">
     <label class="sr-only" for="input-pwd">Password</label>
     <input type="password" name="pwd" id="input-pwd" class="toggle-password form-control" required>
     <div class="input-group-append">
          <div class="toggle-eye input-group-text fa fa-eye"></div>
     </div>
  </div>

for prepend eye field

  <div class="input-group mb-2">
     <div class="input-group-prepend">
        <div class="toggle-eye input-group-text fa fa-eye"></div>
     </div>
     <label class="sr-only" for="input-pwd">Password</label>
     <input type="password" name="pwd" id="input-pwd" class="toggle-password form-control" required>
  </div>

add to your css

.toggle-eye {
    display: flex;
    color: DodgerBlue;
    cursor: pointer;
}

*/
$('.toggle-eye').click(function() {
    $(this).toggleClass('fa-eye fa-eye-slash');

    var $element = $('.toggle-password:first');
    if ($element.attr('type') === 'password') {
        $element.attr('type', 'text');
    } else {
        $element.attr('type', 'password');
    }
});