container_php: str = 'php_package_1'
container_db: str = None

waiting_db_connection: bool = False
phpunit_code_error_bypass: bool = False

containers: list = [
    container_php,
    container_db
]

container_work_dir: str = '/www'

docker_compose_files_list: list = [
    'docker-compose.yaml'
]

commands: dict = {
    'composer install': 'composer install',
    'composer run phpunit': 'composer run phpunit-ci',
    'composer run phpstan': 'composer run phpstan',
    'composer run psalm': 'composer run psalm',
    'composer run phpmd': 'composer run phpmd',
}
