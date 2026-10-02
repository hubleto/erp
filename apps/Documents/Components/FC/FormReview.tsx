import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { type FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';

const componentName = 'FormReview'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Documents';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TabDefault = () => {
  const form = React.useContext(FormMetaContext);
  const idDocument: number = useRecordField('id_document', 0);

  return <>
    <Input
      field='id_document'
      onChange={() => {
        if (form.getRecord().id_version) form.changeField({ field: 'id_version' }, 0);
      }}
    />
    <Input
      key={'id_version_' + (idDocument ?? 0)}
      field='id_version'
      readonly={!idDocument}
      customInputProps={{ endpoint: 'api/record/lookup?idDocument=' + (idDocument ?? 0) }}
    />
    <Input field='requested_on'/>
    <Input field='id_requested_by' />
    <Input field='reviewed_on' />
    <Input field='id_reviewed_by' />
    <Input field='comment' />
    <Input field='id_review_result' />
  </>;
};

const FormReview = (props: FormProps) => <Form
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/Review'}
  urlSlug='documents/reviews'
  title={{field: 'requested_on', sub: T.translate('Document review')}}
  tabs={{default: {content: () => <TabDefault {...props}/>}}}
  {...props}
></Form>;

export default FormReview;
