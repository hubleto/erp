import Translator from '@hubleto/react-ui/core/Translator';
import Form from '@hubleto/react-ui/components/fc/Form';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormHrInterviewProps extends FormProps {}

const componentName = 'FormHrInterview';
const parentApp = 'Hubleto/App/Community/HrRecruitment';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const FormHrInterview = (props: FormHrInterviewProps) => <Form
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/Interview'}
  urlSlug='hr-recruitment/interviews'
  title={{field: 'date_start', sub: T.translate('Interview')}}
  tabs={{default: {content: () => <div className='grid grid-cols-1 md:grid-cols-2 gap-2'>
    <Input field='id_application' />
    <Input field='id_interviewer' />
    <Input field='date_start' />
    <Input field='date_end' />
    <Input field='location' />
    <Input field='status' />
    <Input field='feedback' />
  </div>}}}
  {...props}
/>

export default FormHrInterview;