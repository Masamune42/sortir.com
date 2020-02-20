// Function that allow Firefox Browser to use the dateTime type
var dateTimeStart = document.getElementById('date_time_start');
var dateTimeLimit = document.getElementById('date_time_limit');

var isFirefox = typeof InstallTrigger !== 'undefined';

if (isFirefox) {
    dateTimeStart.innerHTML = `<label for="date_start_firefox">Date de début de sortie</label>
<span class="d-flex flex-wrap">
 <input type="date" id="date_start_firefox" name="date_start_firefox" class="input-group-text" style="margin-right: auto;margin-left: auto"/><input type="time" id="time_start_firefox" name="time_start_firefox" class="input-group-text" style="margin-right: auto;margin-left: auto"/>
</span>
  {{ form_row(outingFormView.startTime) }}`;
    dateTimeLimit.innerHTML = `<label for="date_limit_firefox">Date limite d'inscription</label>
<span class="d-flex flex-wrap">
 <input type="date" id="date_limit_firefox" name="date_limit_firefox" class="input-group-text" style="margin-right: auto;margin-left: auto"/><input type="time" id="time_limit_firefox" name="time_limit_firefox" class="input-group-text" style="margin-right: auto;margin-left: auto"/>
</span>
<div style="display: none">
{{ form_label(outingFormView.limitDateTime) }}
            {{ form_widget(outingFormView.limitDateTime) }}</div>
            {{ form_errors(outingFormView.limitDateTime) }}`;
}


//RadioButton

