`https://github.loc/web/github/v1/{secret-token}/{repository}?payload[event]=pull`

`~/.ssh/config`

`git config core.preloadIndex false`

```shell
Host github github.com
    HostName github.com
    User user # указываем имя пользователя в ОС, в основном это имя узнать через фтп
    IdentityFile ~/.ssh/name_repository # указывать закрытый (private) ключ
```
