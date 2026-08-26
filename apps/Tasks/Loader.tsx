import App from '@hubleto/react-ui/core/App'
import TableTasks from './Components/FC/TableTasks'
import TableTodos from './Components/FC/TableTodos'
import request from "@hubleto/react-ui/core/Request";
import FormCustomizer from '@hubleto/react-ui/core/FormCustomizer';

class Tasks extends App {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('TasksTableTasks', TableTasks);
    globalThis.hubleto.registerReactComponent('TasksTableTodos', TableTodos);

    FormCustomizer.addFormHeaderExtraButton(
      'FormMail',
      'Create task',
      'fas fas fa-list-check',
      (form: any) => {
        request.get(
          'tasks/api/create-from-mail',
          {idMail: form.state.record.id},
          (data: any) => {
            if (data.status == "success") {
              globalThis.window.open(globalThis.hubleto.config.projectUrl + '/tasks/' + data.idTask);
            }
          }
        );
      }
    )
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Enterprise/Tasks', new Tasks());
