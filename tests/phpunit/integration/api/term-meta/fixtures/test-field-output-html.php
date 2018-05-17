<?php
/**
 * HTML output of the test field for use with the render_fields() method test.
 *
 * @package Beans\Framework\Tests\Integration\API\Term_Meta\Fixtures
 *
 * @since 1.5.0
 */

return <<<FIELDSHTML
<tr class="form-field">
	<th scope="row">
			</th>
	<td>
		<div class="bs-field-wrap bs-radio term_meta"><div class="bs-field-inside"><div class="bs-field bs-radio">
<fieldset class="bs-field-fieldset">
	<legend class="bs-field-legend">Layout</legend>
		<label class="" for="beans_layout_test_default_fallback">

	<input id="beans_layout_test_default_fallback" type="radio" name="beans_fields[beans_layout_test]" value="default_fallback" checked='checked' /> 	Use Default Layout		</label>
		<label class="bs-has-image" for="beans_layout_test_c">
					<span class="screen-reader-text">Option for c</span>
			<img src="{$beans_theme_url}lib/admin/assets/images/layouts/c.png" alt="Option for c" />
			<input id="beans_layout_test_c" class="screen-reader-text" type="radio" name="beans_fields[beans_layout_test]" value="c" />
		</label>
		<label class="bs-has-image" for="beans_layout_test_c_sp">
					<span class="screen-reader-text">Option for c_sp</span>
			<img src="{$beans_theme_url}lib/admin/assets/images/layouts/c_sp.png" alt="Option for c_sp" />
			<input id="beans_layout_test_c_sp" class="screen-reader-text" type="radio" name="beans_fields[beans_layout_test]" value="c_sp" />
		</label>
		<label class="bs-has-image" for="beans_layout_test_sp_c">
					<span class="screen-reader-text">Option for sp_c</span>
			<img src="{$beans_theme_url}lib/admin/assets/images/layouts/sp_c.png" alt="Option for sp_c" />
			<input id="beans_layout_test_sp_c" class="screen-reader-text" type="radio" name="beans_fields[beans_layout_test]" value="sp_c" />
		</label>
</fieldset>
</div></div></div>	</td>
</tr>
<tr class="form-field">
	<th scope="row">
		<label for="beans_checkbox_test">Checkbox Test Field Label</label>	</th>
	<td>
		<div class="bs-field-wrap bs-checkbox term_meta"><div class="bs-field-inside"><div class="bs-field bs-checkbox">
<input type="hidden" value="0" name="beans_fields[beans_checkbox_test]" />
<input id="beans_checkbox_test" type="checkbox" name="beans_fields[beans_checkbox_test]" value="1" />
<span class="bs-checkbox-label">Enable the checkbox test</span>
</div></div></div>	</td>
</tr>
<tr class="form-field">
	<th scope="row">
			</th>
	<td>
		<div class="bs-field-wrap bs-text term_meta"><div class="bs-field-inside"><div class="bs-field bs-text"><input id="beans_text_test" type="text" name="beans_fields[beans_text_test]" value="Testing the text field." ></div></div></div>	</td>
</tr>
FIELDSHTML;
