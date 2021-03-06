		<?php wp_footer(); ?>
		<footer>
		<div class="container-fluid">
			<div class="row h-100">
				<div class="col-md-6 d-flex justify-content-center align-items-center h-100">
					<p>Created by J.R. Ruggiero</p>
				</div>
				<div class="col-md-6 d-flex flex-column align-items-center justify-content-center">
					<a href="/">Home</a>
				<?php $list_of_pages = get_pages();
				foreach ($list_of_pages as $page): ?>
					<?php if ($page->post_name !== 'submission-complete') { ?>
					<a href="/<?php echo $page->post_name ?>"><?php echo $page->post_title ?></a>
				<?php } endforeach; ?>
				</div>
			</div>
		</div>
		</footer>
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	</body>
</html>
