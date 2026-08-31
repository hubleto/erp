import App from '@hubleto/react-ui/core/App'
import TableTasks from './Components/FC/TableTasks'
import TableTodos from './Components/FC/TableTodos'
import request from "@hubleto/react-ui/core/Request";
import FormCustomizer from '@hubleto/react-ui/core/FormCustomizer';
import { FormMeta } from '@hubleto/react-ui/components/fc/FormInterfaces';

class Tasks extends App {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('TasksTableTasks', TableTasks);
    globalThis.hubleto.registerReactComponent('TasksTableTodos', TableTodos);

    FormCustomizer.addFormHeaderExtraButton(
      'FormMail',
      (form: FormMeta) => { return form.id <= 0 ? false : {
        title: 'Create task',
        icon: 'fas fas fa-list-check',
        onClick: (form: FormMeta) => {
          request.get(
            'tasks/api/create-from-mail',
            {idMail: form.id},
            (data: any) => {
              if (data.status == "success") {
                globalThis.window.open(globalThis.hubleto.config.projectUrl + '/tasks/' + data.idTask);
              }
            }
          );
        }
      }}
    )
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Enterprise/Tasks', new Tasks());
