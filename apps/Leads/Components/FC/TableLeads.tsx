import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormLead, { FormLeadProps } from './FormLead';

interface TableLeadsProps extends TableProps {
  idCustomer?: number,
}

const componentName = 'TableLeads'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Leads';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableLeads = (props: TableLeadsProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Lead'}
    endpointParams={{idSomeField: props.idCustomer}}
    formUrlSlug='leads'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_customer: props.idCustomer}}
    renderCell={(table: TableMeta, columnName: string, column: any, data: any, options: any) => {
      if (columnName == "virt_tags") {
        return data.TAGS ? data.TAGS.map((tag: any, key: any) => {
          return <div key={key} className="text-nowrap mr-2">
            <i style={{color: tag.TAG?.color}} className="fas fa-tag mr-2"></i>
            {tag.TAG?.name}
          </div>
        }) : null;
      } else if (columnName == "DEAL") {
        if (data.DEAL) {
          return <>
            <a
              className="btn btn-transparent btn-small"
              href={"deals/" + data.DEAL.id}
              target="_blank"
            >
              <span className="icon"><i className="fas fa-arrow-right"></i></span>
              <span className="text">{data.DEAL.identifier}</span>
            </a>
          </>
        } else {
          return null;
        }
      } else {
        return table.renderDefaultCell(columnName, column, data, options);
      }
    }}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormLead {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableLeads;
