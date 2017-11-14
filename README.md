* Usage *

```
/*
 * Example usage
 */
$ftp = new \TheCodingCompany\FTP();
$ftp->connect("<hostname_or_ip_address>", 21)
    ->login("<username>", "<password>")
    ->chdir("/<upload_location>/")
    //->passv()
    //->listFiles()
    ->upload("<full_path_and_filename>")
    ->close();

```