import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormPanel, { FormPanelProps } from './FormPanel';

interface TablePanelsProps extends TableProps {
  idDashboard?: number,
}

const componentName = 'TablePanels'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Dashboards';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TablePanels = (props: TablePanelsProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Panel'}
    endpointParams={{idDashboard: props.idDashboard}}
    formUrlSlug='dashboards/panels'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_dashboard: props.idDashboard}}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormPanel {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>;
}

export default TablePanels;
