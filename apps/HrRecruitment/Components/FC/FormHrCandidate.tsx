import Translator from '@hubleto/react-ui/core/Translator';
import Form from '@hubleto/react-ui/components/fc/Form';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormHrCandidateProps extends FormProps {}

const componentName = 'FormHrCandidate';
const parentApp = 'Hubleto/App/Community/HrRecruitment';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const FormHrCandidate = (props: FormHrCandidateProps) => <Form
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/Candidate'}
  urlSlug='hr-recruitment/candidates'
  title={{fields: ['first_name', 'last_name'], sub: T.translate('Candidate')}}
  tabs={{default: {content: () => <div className='grid grid-cols-1 md:grid-cols-2 gap-2'>
    <Input field='first_name' />
    <Input field='last_name' />
    <Input field='email' />
    <Input field='phone' />
    <Input field='source' />
    <Input field='portfolio_url' />
    <Input field='consent_to_store_data' />
    <Input field='notes' />
  </div>}}}
  {...props}
/>

export default FormHrCandidate;