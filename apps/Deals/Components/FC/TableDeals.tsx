import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { type TableMeta, type TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormDeal from './FormDeal';

interface TableDealsProps extends TableProps {
  idCustomer?: number,
}

const componentName = 'TableDeals'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Deals';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableDeals = (props: TableDealsProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Deal'}
    endpointParams={{idCustomer: props.idCustomer}}
    baseUrlSlug='deals'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_customer: props.idCustomer}}
    showAsCards={true}
    getRowClassName={(table: TableMeta, rowData: any): string => {
      return rowData.is_closed ? 'bg-slate-300' : table.getDefaultRowClassName(rowData);
    }}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormDeal {...table.getDefaultFormProps()}/>;
    }}
    renderCard={(table: TableMeta, record: any, cardIndex: number): React.JSX.Element => {
      const STEP = record.WORKFLOW_STEP;
      return <button
        className={'btn btn-transparent w-1/4 ' + (record.is_closed ? 'striped-45' : '')}
        onClick={() => table.openForm(record.id)}
      >
        <div className="flex flex-col p-2 text-sm font-normal">
          <div className='badge pl-2' style={{borderLeft: '1em solid ' + STEP?.color}}>{STEP?.name}</div>
          <div className="flex-dyn gap-2">
            <div>{record.OWNER?.nick ?? (record.OWNER?.email ?? <div class="text-red-800 text-xs">?</div>)}</div>
            <div>{record.MANAGER?.nick ?? (record.MANAGER?.email ?? <div class="text-red-800 text-xs">?</div>)}</div>
          </div>
          <div className='m-4'>
            <div className="">{record.CUSTOMER?.name}</div>
            <div className="text-lg font-bold">{record.identifier} {record.title}</div>
          </div>
          {!record.virt_next_activity_date ?
            <div className="badge badge-danger text-xs">
              <i className="fas fa-calendar mr-2"></i>
              {T.translate('No future activity is planned.')}
            </div>
          :
            <div className="badge badge-success text-xs">
              <i className="fas fa-calendar mr-2"></i>
              {T.translate('Next activity planned for')} {record.virt_next_activity_date}
            </div>
          }
        </div>
      </button>;
    }}
    {...props}
  ></Table>
}

export default TableDeals;
