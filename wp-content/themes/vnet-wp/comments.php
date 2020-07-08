<?php

/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package vnet-theme
 */


/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */

if (post_password_required()) {
	return;
}



// echo 'commets template';

?>

<div class="form-row">

	<div class="form-col">
		<div class="input-row">
			<div class="input-wrap">
				<input type="text" name="contactName" class="calc-input " value="">
				<span class="placeholder ">Имя</span>
				<span class="underline ">
				</span>
			</div>
			<div class="input-help"></div>
		</div>
	</div>

</div>

<div class="form-row">

	<div class="form-col">
		<div class="input-row">
			<div class="input-wrap">
				<input type="text" name="contatPhone" class="calc-input" value="">
				<span class="placeholder">Телефон</span>
				<span class="underline">
				</span>
			</div>
			<div class="input-help"></div>
		</div>
	</div>

	<div class="form-col">
		<div class="input-row">
			<div class="input-wrap">
				<input type="email" name="contactEmail" class="calc-input" value="">
				<span class="placeholder">E-mail</span>
				<span class="underline">
				</span>
			</div>
			<div class="input-help"></div>
		</div>
	</div>

</div>


<div class="form-row">

	<div class="form-col">
		<div class="input-row">
			<div class="input-wrap">
				<textarea name="contactMessage" class="calc-input"></textarea>
				<span class="placeholder">Сообщение</span>
				<span class="underline">
				</span>
			</div>
			<div class="input-help"></div>
		</div>
	</div>

</div>
<?php
