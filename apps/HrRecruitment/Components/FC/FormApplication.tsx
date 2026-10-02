import Translator from '@hubleto/react-ui/core/Translator';
import Form from '@hubleto/react-ui/components/fc/Form';
import { type FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

const componentName = 'FormApplication';
const parentApp = 'Hubleto/App/Community/HrRecruitment';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const FormApplication = (props: FormProps) => <Form
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/Application'}
  urlSlug='hr-recruitment/applications'
  title={{field: 'date_applied', sub: T.translate('Application')}}
  tabs={{default: {content: () => <div className='grid grid-cols-1 md:grid-cols-2 gap-2'>
    <Input field='id_job_opening' />
    <Input field='id_candidate' />
    <Input field='id_workflow_step' />
    <Input field='date_applied' />
    <Input field='date_decided' />
    <Input field='notes' />
  </div>}}}
  {...props}
/>

export default FormApplication;