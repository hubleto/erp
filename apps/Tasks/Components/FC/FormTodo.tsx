import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormTodoProps extends FormProps {}

const componentName = 'FormTodo'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Tasks';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormTodoProps) => {
  const form = React.useContext(FormMetaContext);
  return <>
    <Input field='id_task' />
    <Input field='id_responsible' />
    <Input field='todo' />
    <Input field='is_closed' />
    <Input field='date_deadline' />
  </>;
}

/** FormTodo */
const FormTodo = (props: FormTodoProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Todo'}
    urlSlug='tasks/todo'
    title={{field: 'todo', sub: T.translate('Todo')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormTodo;
