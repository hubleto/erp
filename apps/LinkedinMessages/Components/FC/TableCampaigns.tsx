import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { type TableMeta, type TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormCampaign, { FormCampaignProps } from './FormCampaign';

interface TableCampaignsProps extends TableProps {
}

const componentName = 'TableCampaigns'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Custom/LinkedinMessages';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableCampaigns = (props: TableCampaignsProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Campaign'}
    endpointParams={{}}
    baseUrlSlug='linkedin-messages/campaigns'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{}}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormCampaign {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableCampaigns;
