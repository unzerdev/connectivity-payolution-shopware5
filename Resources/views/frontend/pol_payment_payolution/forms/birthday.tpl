{namespace name="frontend/pol_payment_payolution/main"}

{assign var="birthdayParts" value='-'|explode:$sUserData.additional.user.birthday}
<div class="block-group payolution-form--birthdate">
    <label for="register_personal_birthdate{$payment_mean.id}" class="birthday--label">{s name='form/birthday/heading'}Geburtstag{/s}</label>
    <div class="block birthdate--day">
        <select id='register_personal_birthdate{$payment_mean.id}' name="payolution[{$payolutionPaymentMethod}][birthday]">
            <option value="">{s name='form/birthday/field/day/label'}Tag{/s}</option>
            {section name="birthdate" start=1 loop=32 step=1}
                <option value="{$smarty.section.birthdate.index}" {if $smarty.section.birthdate.index == $birthdayParts[2]}selected='selected'{/if}>{$smarty.section.birthdate.index}</option>
            {/section}
        </select>
    </div>

    <div class="block birthdate--month">
        <select name="payolution[{$payolutionPaymentMethod}][birthmonth]">
            <option value="">{s name='form/birthday/field/month/label'}Monat{/s}</option>
            {section name="birthmonth" start=1 loop=13 step=1}
                <option value="{$smarty.section.birthmonth.index}" {if $smarty.section.birthmonth.index == $birthdayParts[1]}selected='selected'{/if}>{$smarty.section.birthmonth.index}</option>
            {/section}
        </select>
    </div>

    <div class="block birthdate--year">
        <select name="payolution[{$payolutionPaymentMethod}][birthyear]">
            <option value="">{s name='form/birthday/field/year/label'}Jahr{/s}</option>
            {section name="birthyear" loop={$smarty.now|date_format:"%Y"} max={$smarty.now|date_format:"%Y"}-1900 step=-1}
                <option value="{$smarty.section.birthyear.index}" {if $smarty.section.birthyear.index == $birthdayParts[0]}selected='selected'{/if}>{$smarty.section.birthyear.index}</option>
            {/section}
        </select>
    </div>
</div>
