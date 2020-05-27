<div class="wrap">
    <h1>Udemy Importer - Affiliate API</h1>

    <p>Please enter your search term or select category to get course list from Udemy.com</p>

    <form id="udemy-search-form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
        <input type="hidden" name="action" value="slui_get_data_from_udemy">
        <input type="hidden" id="url" name="url" value="<?php echo admin_url('admin-post.php'); ?>">
        <table class="form-table" role="presentation">
            <tbody>
                <tr class="example-class">
                    <th width="15%"><label for="search">Search Term</label></td>
                    <td width="35%"><input type="text" id="search" name="search" value="" placeholder="" style="width: 100%"></td>
                    <th width="15%" style="padding-left:20px"><label for="category">Category</label></th>
                    <td width="25%">
                        <select id="category" name="category" class="regular-text">
                            <option>All</option>
                            <option>Business</option>
                            <option>Design</option>
                            <option>Development</option>
                            <option>Finance & Accounting</option>
                            <option>Health & Fitness</option>
                            <option>IT & Software</option>
                            <option>Lifestyle</option>
                            <option>Marketing</option>
                            <option>Music</option>
                            <option>Office Productivity</option>
                            <option>Personal Development</option>
                            <option>Photography</option>
                            <option>Teaching & Academics</option>
                            <option>Udemy Free Resource Center</option>
                            <option>Vodafone</option>
                        </select>
                    </td>
                    <td width="10%"><input type="submit" name="submit" id="submit" class="button button-primary" style="width: 100%" value="Search"></td>
                </tr>
            </tbody>
        </table>

    </form>

    <div id="notice">

    </div>
    <div id="data">

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function(e) {
        let searchForm = document.getElementById('udemy-search-form');

        searchForm.addEventListener('submit', (e) => {
            e.preventDefault();

            let params = new URLSearchParams(new FormData(searchForm));
            let url = "<?php echo admin_url('admin-ajax.php'); ?>";

            document.getElementById('data').innerHTML = '<div class="load-spinner"><p>Please Wait... Data Collecting from Udemy API. </p><img src="<?php echo admin_url('images/loading.gif'); ?>" /></div>';

            fetch(url, {
                    method: "POST",
                    body: params
                })
                .then(res => res.text())
                .then(result => {
                    document.getElementById('data').innerHTML = result;
                })
                .catch(error => {
                    document.getElementById('data').innerHTML = '<div class="error notice is-dismissible" ><p><strong>Error!</strong> API request failed.</p></div>';
                })
        });
    });

    function importCourse(id) {
        let data = new FormData();
        data.append('action', 'slui_add_course_from_udemy');
        data.append('url', "<?php echo admin_url('admin-post.php'); ?>");
        data.append('id', id);

        let url = "<?php echo admin_url('admin-ajax.php'); ?>";

        document.getElementById('div-' + id).innerHTML = '<img src="<?php echo admin_url('images/loading.gif'); ?>" /> Importing';

        fetch(url, {
                method: "POST",
                body: new URLSearchParams(data)
            })
            .then(res => res.text())
            .then(result => {
                console.log(result);
                if (result != 0) {
                    document.getElementById('notice').innerHTML = '<div class="notice-success notice is-dismissible" ><p><strong>Success!</strong> Course imported successfully.</p></div>';
                    document.getElementById('div-' + id).innerHTML = '<p style="color: #39b54a; font-weight: bold;">Imported</p>'
                } else {
                    document.getElementById('div-' + id).innerHTML = '<button class="button button-primary" value="Import" onClick="importCourse(' + id + ')">Import</button>';
                }
            })
            .catch(error => {
                document.getElementById('notice').innerHTML = '<div class="error notice is-dismissible" ><p><strong>Error!</strong> API request failed.</p></div>';
            })
    }
</script>