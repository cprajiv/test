<?php

// For full width page
if(FILE_NAME!=='login.php')
{ ?>
	</div>
<?php } ?>
<!--End of page wapper-->
</div>
<!--End of wrapper-->



<!-- Metis Menu Plugin JavaScript -->
<script src="bower_components/metisMenu/dist/metisMenu.min.js"></script>

<!-- Morris Charts JavaScript -->
<script src="bower_components/raphael/raphael-min.js"></script>
<!--<script src="bower_components/morrisjs/morris.min.js"></script>
<script src="js/morris-data.js"></script>-->

<!-- Custom Theme JavaScript -->
<script src="dist/js/sb-admin-2.js"></script>

 <script src="bower_components/DataTables/media/js/jquery.dataTables.min.js"></script>
    <script src="bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>

 <script>
    $(document).ready(function() {
        $('#dataTables-example').DataTable({
                responsive: true,
				iDisplayLength: 25
        });
    });
    </script>

</body>

</html>

<?php  ob_flush(); ?>
