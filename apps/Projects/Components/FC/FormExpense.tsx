import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormExpenseProps extends FormProps {}

const componentName = 'FormExpense'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Projects';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormExpenseProps) => {
  return <div>
    <Input field='id_project' />
    <Input field='reason' />
    <Input field='date' />
    <Input field='amount' />
    <Input field='id_approved_by' />
    <Input field='id_spent_by' />
  </div>;
}

/** FormExpense */
const FormExpense = (props: FormExpenseProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Expense'}
    urlSlug='projects/expenses'
    title={{field: 'reason', sub: T.translate('Expense')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormExpense;
