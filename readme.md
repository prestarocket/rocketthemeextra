#Prestarocket theme extra
this module allows you to reset theme setting (in theme.yml) and add extra configuration in theme.yml for unhook modules.
```yml

global_settings:
  configuration:
    PS_IMAGE_QUALITY: png
  modules:
   to_enable:
     - ps_linklist
   to_disable:
     - ps_searchbar
   to_unhook:
    displayNav2:
     - ps_customersignin
     - ps_shoppingcart
 ```

