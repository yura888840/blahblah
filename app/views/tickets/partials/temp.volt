<div class="ticket-col-inner">
    <form>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs nav-tabs-ticket">
            <li class="active"><a class="ticket-height-reload" href="#public-notes" data-toggle="tab">Public</a></li>
            <li><a class="ticket-height-reload" href="#provide-notes" data-toggle="tab">Private</a></li>
            <li class="dropdown hidden-xs">
                <a href="#" data-toggle="dropdown">Use Template <i class="fa fa-angle-up"></i></a>
                <ul class="dropdown-menu not-hide dropdown-show-top" role="menu">

                    <!-- МОМЕНТАЛЬНЫЙ РЕЗУЛЬТАТ AJAX ПОИСКА -->
                    <li><a href="#">Thanks for feedback + Resolve</a></li>
                    <li><a href="#">Thanks for feedback + Resolve Thanks for feedback + Resolve</a></li>
                    <!-- /МОМЕНТАЛЬНЫЙ РЕЗУЛЬТАТ AJAX ПОИСКА -->

                    <li class="dropdown-form">
                        <input class="form-control" placeholder="Search">
                    </li>
                    <li class="divider"></li>
                    <li class="dropdown dropdown-submenu">
                        <a href="#" data-toggle="dropdown">Sample Template</a>
                        <ul class="dropdown-menu">
                            <li><a href="#">Thanks for feedback + Resolve</a></li>
                            <li><a href="#">Thanks for feedback + Resolve</a></li>
                            <li><a href="#">Thanks for feedback + Resolve Thanks for feedback + Resolve</a></li>
                            <li><a href="#">Thanks for feedback + Resolve Thanks for feedback + Resolve Thanks for
                                    feedback + Resolve</a></li>
                            <li><a href="#">Thanks for feedback + Resolve</a></li>
                            <li><a href="#">Thanks</a></li>
                            <li><a href="#">Thanks</a></li>
                            <li><a href="#">Thanks for feedback + Resolve</a></li>
                            <li><a href="#">Thanks for feedback + Resolve</a></li>
                            <li><a href="#">Thanks for feedback + Resolve</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>

        <!-- -->
        <div class="ticket-form-group">
            <div class="tab-content">
                <div class="tab-pane active" id="public-notes">
                    <select name="notifiers" class="form-control" multiple="multiple"
                            data-placeholder="Choose watchers...">
                        <option value="54e7350d51be0bef9d8b4567" selected>crm-master@ecomitize.com</option>
                        <option value="54eb928151be0bec9d8b4567">yura888840@gmail.com</option>
                        <option value="54edccf151be0be79d8b4568">yuri.oblovatskyy@ecomitize.com</option>
                        <option value="54eddce351be0bec9d8b4568">ks275</option>
                        <option value="54eded1b51be0bef9d8b456c">ks2</option>
                        <option value="54f092b951be0bcd9d8b456a">Olga Loboda</option>
                        <option value="5500422d51be0bbd0d8b4567">sergey</option>
                        <option value="5501b6e651be0ba30d8b4567">Alex</option>
                        <option value="5506b28c51be0bae0d8b4567">Alex Second</option>
                        <option value="5506ec9f51be0b9c0d8b4567">Alex 3</option>
                        <option value="5507fb0551be0bd50d8b4567">Julius</option>
                        <option value="550800e351be0bcc0d8b4567">Alex 7878</option>
                        <option value="550c102c51be0b970d8b4567">superadmin@ecomitize.com</option>
                        <option value="55185bf251be0b4b548b456a">Alex Babanski</option>
                        <option value="551ea2f751be0b022b8b4568">DmitryB</option>
                        <option value="5521f15651be0b8dd38b4567">Andrew Balitsky</option>
                        <option value="553ffff851be0b57058b4567">Jacob North</option>
                        <option value="5554bdb3c9a5995f017055b2">e- mail</option>
                    </select>
                    <input class="form-control" placeholder="" value="Re: {{ ticket['subject'] }}">

                    <div class="input-group">
                        <textarea class="form-control textarea-resize-none ticket-textarea textarea-autosize"
                                  placeholder="Enter your public reply..."></textarea>
                        <span class="input-group-addon btn btn-primary ticket-btn-send"><i
                                    class="fa fa-long-arrow-up"></i></span>
                    </div>
                </div>
                <div class="tab-pane" id="provide-notes">
                    <div class="input-group">
                        <textarea id="replytext"
                                  class="form-control textarea-resize-none ticket-textarea textarea-autosize"
                                  placeholder="Enter your private reply..."></textarea>
                        <span class="input-group-addon btn btn-primary ticket-btn-send"><i
                                    class="fa fa-long-arrow-up"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <!-- -->

    </form>
</div>