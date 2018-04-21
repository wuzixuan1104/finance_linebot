# Welcome to ADPost

## 產生 apiDoc 文件

1. 使用指令編譯
	* 安裝 [apiDoc](http://apidocjs.com/) `sudo npm install apidoc -g`
	* 專案目錄下指令 `apidoc -i app/controller/api`
	* 若要加入 Template 則加入參數 **`-t`**

2. 使用 Gulp
	* 先安裝 [Gulp](https://gulpjs.com/)
	* 在專案目錄的 `cmd` 內執行 `npm install .` 初始 gulp 專案
	* 專案目錄下的 `cmd` 執行指令 `gulp api`

3. 部署 apiDoc 文件至 `doc.adpost.com.tw`
	1. 專案進入 `cmd` 資料夾後，執行指令 `gulp api` 編譯 API 文件
	2. 產生的 API 文件在專案目錄 `doc` 下
	3. 進入 `doc` 目錄，並且執行指令 `php put.php -b doc.adpost.com.tw -a {access key} -s {secret key}`