<!-- BLOCK DICTON-->

<div id="dicton_block_left" class="block">
    <h4>Dicton du jour <i class="fa fa-comment" aria-hidden="true"></i></h4>
    <div class="block_content">
        <div id="date_saint">
            <p>
                {$smarty.now|date_format:'%d %B %Y'} <br/>-<br/> 
                {if $gender == 0}
                    Saint 
                {else}
                    Sainte 
                {/if}
                {$saint}
            </p>
        </div>
        <div id="quote_of_the_day">
            <p>
                {$today_proverb}
            </p>
        </div>
        <div id="custom_advice_jarditou">
            <p>
                {$today_advice}
            </p>
        </div>
    </div>




</div>

<!-- /BLOCK DICTON -->