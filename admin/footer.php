	</div>
		<footer class="main-footer">
			<strong>Developed by NodeTent</strong>
		</footer>
	</div>
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
	<script src="js/dataTables.bootstrap.min.js"></script>
	<script src="js/jquery.slimscroll.min.js"></script>
	<script src="js/app.min.js"></script>
	<script>
	  $(function () {
	    $("#table").DataTable();
	    $('#confirm-delete').on('show.bs.modal', function(e) {
	      $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
	    });
	  });
	</script>
</body>
</html>