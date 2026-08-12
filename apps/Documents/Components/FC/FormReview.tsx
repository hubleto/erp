import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormReviewProps extends FormProps {}

const componentName = 'FormReview'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Documents';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormReviewProps) => {
  return <>
    <Input field='id_document' />
    <Input field='id_version' />
    <Input field='requested_on' />
    <Input field='id_requested_by' />
    <Input field='reviewed_on' />
    <Input field='id_reviewed_by' />
    <Input field='comments' />
  </>;
}

/** FormReview */
const FormReview = (props: FormReviewProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Review'}
    urlSlug='documents/reviews'
    endpointParams={{}}
    onAfterFormInitialized={(form: any) => {}}
    title={{field: 'name', sub: T.translate('Document review')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormReview;
