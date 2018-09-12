# 歡迎來到 Maple
🍁 飛起來 🍁 飛過來

## 說明
* 這是一套 [OA Wu](https://www.ioa.tw/) 所製作的個人 [PHP](http://php.net/) 框架！  
* 主功能是快速架構後台、Migration 管理、使用 Active Record 式的 Model，加入 [apiDoc](http://apidocjs.com/)、使用 [Deployer](https://deploye4.r.org/) 快速部署至伺服器 等功能。  
* 此框架需要搭配 `maple` 指令操作，來進行相關功能，文件中會說明該如何使用。
* 初始架構參考 [CodeIgniter](https://www.codeigniter.com/) 與 [OACI](https://github.com/comdan66/oaci)。
* 此框架主要支援 PHP 5.6（包含）以上。  

## Maple 指令
下載 Maple phar，請打開終端機，依序執行以下指令完後，重新開啟終端機即可。

* 下載 `curl -LO https://comdan66.github.io/Maple/maple`
* 搬移 `mv maple /usr/local/bin/maple`
* 權限 `chmod +x /usr/local/bin/maple`

## 初始化專案
專案最初開始通常需要一些結構目錄的建置，例如 cache、log 等目錄，所以需要執行初始動作，在 Maple 框架下只需要打開終端機，執行指令 `maple init` 後，依據所需即可建立初始所需的目錄結構。


## 部署專案
專案部署更新至伺服器前請先確認以下幾項步驟：

1. 請至伺服器設定專案，將專案建置起來，確認專案可以正常使用 `git pull` 與 `maple` 指令，以及初始伺服器的專案架構。 

2. 因為部署過程中是使用 [SSH](https://zh.wikipedia.org/wiki/Secure_Shell) 方式連線，所以請檢查本地端是否可以使用公鑰的方式連線至伺服器，若不是請將本地端機器的公鑰新增至伺服器 `~/.ssh/authorized_keys` 是否有設定部署的本地端機器的公鑰。

3. 請先安裝部署工具 [Deployer](https://deployer.org/)，可以執行以下指令安裝：
	* `curl -LO https://deployer.org/deployer.phar`
	* `mv deployer.phar /usr/local/bin/dep`
	* `chmod +x /usr/local/bin/dep`

4. 請在專案下設定 `deploy.php` 的 config，可以依據初始時的環境新增至不同的 config 目錄。
> 舉例，若是環境為 `development`，就複製 `app/config/deploy.php` 至 `app/config/development/deploy.php` 並修改內容。

確認以上步驟後，即可使用 Maple 指令部署，在專案目錄下打開終端機，執行指令 `maple deploy` 後，選擇部署的類型後即可開始部署。

## 新增 Migration
在專案目錄下打開終端機，執行指令 `maple create migration` 即可。

## 新增 Model
在專案目錄下打開終端機，執行指令 `maple create model` 即可。

## 執行 Migration
在專案目錄下打開終端機，執行指令 `maple migration` 即可。

> 更新至最新版可以下指令 `maple migration new`。  
> 更新至最初版(歸零)可以下指令 `maple migration ori`。

## API 文件
### 產文件
1. 先安裝 [Gulp](https://gulpjs.com/)。
2. 至專案目錄下的 `doc` 執行指令 `npm install .` 初始 gulp 專案。
3. 至專案目錄下的 `doc` 執行指令 `gulp api` 開始產生文件。

### 部署
API 文件部署更新至 S3 前請先確認是否有設定 config，若未設定的話，請在專案下設定 `apiDoc.php` 的 config，可以依據初始時的環境新增至不同的 config 目錄，然後在專案目錄下打開終端機，執行指令 `maple apiDoc`。
> 舉例，若是環境為 `development`，就複製 `app/config/apiDoc.php ` 至 `app/config/development/apiDoc.php` 並修改內容。
